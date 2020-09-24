<?php

    define("RATING_THRESHOLD", 1);

    require(__DIR__ . '/csrf-handler.php');
    if(!isset($_POST))
    {
        http_response_code(400);
        echo "400";
        return;
    }
    else if(!verify_csrf($_POST))
    {
        http_response_code(403);
        header("Location: /pages/errors/403.php");
        return;
    }



    function getCaptions($repo, $vidID)
    {        
        // Create a stream
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n" .
                    "User-Agent: YouCap"
            ]
        ];

        $context = stream_context_create($opts);
        
        $infoFile = file_get_contents("https://raw.githubusercontent.com/YouCap/$repo/master/review/$vidID", false, $context);
        $json = json_decode($infoFile, true);
                                
        $content = $json["contents"];
        $captions = base64_decode($content);
        
        return $captions;
    }



    require(__DIR__ . '/creds.php');
    $conn = mysqliConnection();

    $language = strtolower($_POST["language"]);
    $vidID = $_POST["vidID"];
    $user = $_POST["user"];
    $email = $_POST["email"];
    $rating = $_POST["rating"];

    if(!is_numeric($rating) || (intval($rating) !== 1 && intval($rating) !== -1))
    {
        http_response_code(400);
        echo "Error 400: Bad Request.";
        return;
    }

    $language = mysqli_real_escape_string($conn, $language);
    $vidID = mysqli_real_escape_string($conn, $vidID);
    $email = mysqli_real_escape_string($conn, $email);


    $sql = "SELECT `vidID` FROM `$language` WHERE `vidID`=\"$vidID\" AND `users` LIKE \"%$email%\"";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if(sizeof(mysqli_fetch_array($result)) > 0)
    {
        http_response_code(403);
        return;
    }




    $sql = "UPDATE `$language` SET `rating`=`rating` + $rating,`users`=CONCAT(`users`, \",$email\") WHERE `vidID`=\"$vidID\"";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = "SELECT `repoID`, `rating`, `sha` FROM `$language` WHERE `vidID`=\"$vidID\"";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $row = mysqli_fetch_array($result);
    $repo = "captions-$language-" . $row["repoID"];

    if(sizeof($row) > 0)
    {
        $client = githubClient();
        $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');
        
        if($row["rating"] >= abs(RATING_THRESHOLD))
        {
            if($row["rating"] >= RATING_THRESHOLD)
            {
                $content = getCaptions($repo, $vidID);
                $fileInfo = $client->api('repo')->contents()->create('YouCap', $repo, "published/$vidID", $content, "Committed by YouCap website", "master", $committer);
            }
            
            $fileInfo = $client->api('repo')->contents()->rm('YouCap', $repo, "review/$vidID", "Rating reached negative threshold. File removed by YouCap Website.", $row['sha'], "master", $committer);

            $sql = "DELETE FROM `$language` WHERE `vidID`=\"$vidID\"";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }
        
        session_destroy();
    }

?>
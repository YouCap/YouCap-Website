<?php

    define("RATING_THRESHOLD", 3);

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

    $language = $_POST["language"];
    $vidID = $_POST["vidID"];
    $email = $_POST["email"];
    $rating = $_POST["rating"];

    if($rating !== 1 && $rating !== -1)
    {
        http_response_code(400);
        echo "Error 400: Bad Request.";
        return;
    }

    $language = mysqli_real_escape_string($language);
    $vidID = mysqli_real_escape_string($vidID);
    $email = mysqli_real_escape_string($email);


    $sql = "UPDATE `$language` SET `rating`=`rating` + $rating,`users`=CONCAT(`users`, \",$email\") WHERE `vidID`=\"$vidID\"";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = "SELECT `repoID`, `rating`, `sha` FROM `$language` WHERE `vidID` IS \"$vidID\"";
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
                $fileInfo = $client->api('repo')->contents()->create('YouCap', $repo, "published/$vidID", $content, "Committed by $user", "master", $committer);
            }

            $fileInfo = $client->api('repo')->contents()->rm('YouCap', $repo, "review/$vidID", "Rating reached negative threshold. File removed by YouCap Website.", $row['sha'], "master", $committer);

            $sql = "DELETE FROM `$language` WHERE `vidID`=\"$vidID\"";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }
    }

?>
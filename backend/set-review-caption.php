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
        
        $infoFile = file_get_contents("https://raw.githubusercontent.com/YouCap/$repo/main/review/$vidID", false, $context);
        $json = json_decode($infoFile, true);
                                
        return filter_var($json["contents"], FILTER_SANITIZE_STRING);        
    }



    require(__DIR__ . '/creds.php');
    $conn = mysqliConnection();

    $language = preg_replace('/\s+/', '_', strtolower($_POST["language"]));
    $vidID = $_POST["vidID"];
    $user = $_POST["user"];
    $id = $_POST["id"];
    $rating = $_POST["rating"];

    # Validate Google User ID
    $payload = validateGoogleIDToken($id);
    if(!$payload)
    {
        http_response_code(403);
        echo "403";
        return;
    }
    else
    {
        $id = $payload['sub'];
    }

    if(!is_numeric($rating) || (intval($rating) !== 1 && intval($rating) !== -1))
    {
        http_response_code(400);
        echo "Error 400: Bad Request.";
        return;
    }

    $language = mysqli_real_escape_string($conn, $language);
    $vidID = mysqli_real_escape_string($conn, $vidID);
    $id = mysqli_real_escape_string($conn, $id);


    $sql = "SELECT `vidID` FROM `$sqlReviewDatabase` WHERE `vidID`=\"$vidID\" AND `language`=\"$language\" AND `users` LIKE \"%$id%\"";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if(sizeof(mysqli_fetch_array($result)) > 0)
    {
        http_response_code(403);
        return;
    }




    $sql = "UPDATE `$sqlReviewDatabase` SET `rating`=`rating` + $rating,`users`=CONCAT(`users`, \",$id\") WHERE `vidID`=\"$vidID\" AND `language`=\"$language\"";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = "SELECT `repoID`, `rating`, `sha` FROM `$sqlReviewDatabase` WHERE `vidID`=\"$vidID\" AND `language`=\"$language\"";
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
                $content = htmlspecialchars(getCaptions($repo, $vidID), ENT_QUOTES);
                $fileInfo = $client->api('repo')->contents()->create('YouCap', $repo, "published/$vidID", $content, "Committed by YouCap website", "main", $committer);
            }
            
            $fileInfo = $client->api('repo')->contents()->rm('YouCap', $repo, "review/$vidID", "Rating reached negative threshold. File removed by YouCap Website.", $row['sha'], "main", $committer);

            $sql = "DELETE FROM `$language` WHERE `vidID`=\"$vidID\"";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }
        
        session_destroy();
    }

?>
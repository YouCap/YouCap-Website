<?php
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
        echo "403";
        return;
    }

    require_once $_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php';

    require(__DIR__ . '/creds.php');
    $client = githubClient();

    $vidID = $_POST["fileName"];
    $path = "review/" . $vidID;
    $captionContent = base64_encode($_POST["content"]);
    $user = $_POST["user"];
    $email = $_POST["email"];
    $language = strtolower($_POST["vid-lang-name"]);
    $nsfw = $_POST["nsfw"];

    $settings = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/backend/youcap-info.json"), true);
        
    $repoNum = 0;
    foreach($settings["languages"] as $languageOBJ)
    {
        if(strtolower($languageOBJ["name"]) == $language)
        {
            $repoNum = $languageOBJ["max-review-repo-number"];
        }
    }



    $content = "{
        \"author\": \"$user\",
        \"language\": \"$language\",
        \"contents\": \"$captionContent\",
        \"nsfw\": $nsfw
    }";


    $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');
    $fileInfo = $client->api('repo')->contents()->create('YouCap', "captions-$language-$repoNum", $path, $content, "Committed by YouCap website", "master", $committer);
    

    //From creds.php
    $conn = mysqliConnection();

    $language = mysqli_real_escape_string($conn, $language);
    $vidID = mysqli_real_escape_string($conn, $vidID);
    $email = mysqli_real_escape_string($conn, $email);
    $nsfw = mysqli_real_escape_string($conn, $nsfw);
    $sha = $fileInfo["content"]["sha"];

    $sql = "INSERT INTO `$language`(`vidID`, `repoID`, `rating`, `users`, `sha`, `filters`) VALUES ('$vidID', $repoNum, 0, '$email', '$sha', 'nsfw=$nsfw')";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

?>
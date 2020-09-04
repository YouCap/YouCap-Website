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
        header("Location: /pages/errors/403.php");
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
        'author': '$user',
        'language': '$language',
        'contents': '$captionContent',
        'nsfw': false
    }";


    $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');
    $fileInfo = $client->api('repo')->contents()->create('YouCap', "captions-$language-$repoNum", $path, $content, "Committed by $user", "master", $committer);

    

    //From creds.php
    $conn = mysqliConnection();

    $language = mysqli_real_escape_string($language);
    $vidID = mysqli_real_escape_string($vidID);
    $email = mysqli_real_escape_string($email);
    $sha = $fileInfo["sha"];

    $sql = "INSERT INTO `$language`(`vidID`, `repoID`, `rating`, `users`, `sha`) VALUES ($vidID, $repoNum, 0, $email, $sha)";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

?>
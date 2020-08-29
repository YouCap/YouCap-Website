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

    $path = $_POST["fileName"];
    $content = $_POST["content"];
    $user = $_POST["user"];

    $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');
    $fileInfo = $client->api('repo')->contents()->create('YouCap', 'captions-english-0', $path, $content, "Committed by $user", "master", $committer);
?>
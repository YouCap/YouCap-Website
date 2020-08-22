<?php
    require(__DIR__ . '/csrf_handler.php');
    if(!isset($_POST) || !verify_csrf($_POST))
        return;

    require_once $_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php';

    require(__DIR__ . '/creds.php');
    $client = githubClient();

    $path = $_POST["fileName"];
    $content = $_POST["content"];
    $user = $_POST["user"];

    $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');
    $fileInfo = $client->api('repo')->contents()->create('YouCap', 'captions-english-0', $path, $content, "Committed by $user", "master", $committer);
?>
<?php
    require(__DIR__ . '/backend/creds.php');
    $client = githubClient();

    $content = $_POST["content"];
    $user = $_POST["user"];

    $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');
    $fileInfo = $client->api('repo')->contents()->create('YouCap', 'captions-english-0', '/', $content, "Committed by $user", "master", $committer);
?>
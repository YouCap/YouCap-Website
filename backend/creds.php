<?php
    function githubCreds() {
        $json = json_decode(file_get_contents(__DIR__ . "/secrets/secrets.json"));
        return $json["github"];
    }
?>
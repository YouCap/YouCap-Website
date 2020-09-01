<?php
    function chooseID($language) {
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
        
        $json = json_decode(file_get_contents("https://api.github.com/repos/YouCap/captions-$language-0/contents", false, $context), true);

        return $json[rand(1, sizeof($json))]["name"];
    }

    function getCaptions($repo, $vidID) {
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
        
        return file_get_contents("https://raw.githubusercontent.com/YouCap/$repo/$vidID");
    }

    #chooseID($_GET['vid-lang-name']);
    echo getCaptions("captions-english-0", "jtXw0VnW9l4");

?>
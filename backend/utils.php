<?php
    # Get the text of a video's captions
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
                                
        return $json["contents"];
    }

    # Get the text of a video's captions, filtered to prevent XSS
    function getCaptionsFiltered($repo, $vidID)
    {
        # Filters the captions
        return htmlspecialchars(getCaptions($repo, $vidID), ENT_QUOTES);
    }
?>
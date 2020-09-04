<?php
    require(__DIR__ . '/creds.php');

    if(!isset($_SESSION))
        session_start();

    function chooseID($language, $user)
    {        
        $language = mysqli_real_escape_string($language);
        $user = mysqli_real_escape_string($user);
        
        // Create connection (From creds.php)
        $conn = mysqliConnection();
        
        $sql = 
        "SELECT * FROM `$language` WHERE `users` NOT LIKE '%$user%' ORDER BY RAND() LIMIT 1";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $row = mysqli_fetch_array($result);
        
        if(sizeof($row) > 0)
        {
            return $row["vidID"];
        }
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

    $vidID = "";
    $repoID = 0;

    if(!isset($_SESSION["cache-review-id"]) || $_SESSION["cache-review-id"] == "")
    {
        $vidInfo = chooseID($_GET['vid-lang-name']);
                
        $vidID = $vidInfo[0];
        $repoID = $vidInfo[1];
        
        $_SESSION["cache-review-id"] = htmlspecialchars($vidID);
        $_SESSION["cache-review-repo-id"] = $repoID;
    }
    else
    {
        $vidID = $_SESSION["cache-review-id"];
        $repoID = $_SESSION["cache-review-repo-id"];
    }

    if(isset($_GET["print"]))
        echo htmlspecialchars(getCaptions("captions-" . $_GET["vid-lang-name"] . "-$repoID", $vidID));

?>
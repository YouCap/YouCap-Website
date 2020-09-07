<?php
    require(__DIR__ . '/creds.php');

    if(!isset($_SESSION))
        session_start();

    function chooseID($language, $user, $filters)
    {        
        // Create connection (From creds.php)
        $conn = mysqliConnection();
        
        $language = mysqli_real_escape_string($conn, $language);
        $user = mysqli_real_escape_string($conn, $user);
        
        $sql = 
        "SELECT * FROM `$language` WHERE `users` NOT LIKE '%$user%'";
        
        if($filters["nsfw"] == "false")
            $sql = $sql . " AND `filters` LIKE '%nsfw=false%'";
        
        $sql = $sql . " ORDER BY RAND() LIMIT 1";
        
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                
        $row = mysqli_fetch_array($result);
                        
        if(sizeof($row) > 0)
        {
            return array($row["vidID"], $row["repoID"]);
        }
        
        return ["-1", "-1"];
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
    $vidInfo = null;

    if(!isset($_SESSION["cache-review-id"]) || $_SESSION["cache-review-id"] == "" || $_SESSION["cache-review-id"] == "-1")
    {
        if(isset($_POST["vid-lang-name"]))
            $vidInfo = chooseID(strtolower($_POST['vid-lang-name']), $_POST["user"], array(
                "nsfw" => isset($_POST["nsfw"]) ? $_POST["nsfw"] : "false"
            ));
        else
            $vidInfo = chooseID(strtolower($_GET['vid-lang-name']), $_GET["user"], array(
                "nsfw" => isset($_GET["nsfw"]) ? $_GET["nsfw"] : "false"
            ));
                        
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

    if(isset($_GET))
        $print = isset($_GET["print"]);

    if(isset($print) && $print && $vidID != "-1")
        echo htmlspecialchars(getCaptions("captions-" . $_GET["vid-lang-name"] . "-$repoID", $vidID));

?>
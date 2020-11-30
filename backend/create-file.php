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

    # Get all of the POST variables.
    $vidID = $_POST["fileName"];
    $path = "review/" . $vidID;

    # Filters HTML tags to prevent XSS
    $captionContent = htmlspecialchars($_POST["content"], ENT_QUOTES);
    $user = htmlspecialchars(/*$_POST["user"]*/"Anonymous", ENT_QUOTES);

    # Get the username and ID
    $id = $_POST["id"];

    $language = preg_replace('/\s+/', '_', strtolower($_POST["vid-lang-name"]));
    $nsfw = strtolower($_POST["nsfw"]);

    # Validate filter input
    if($nsfw != "true" && $nsfw != "false")
    {
        http_response_code(400);
        echo "400";
        return;
    }

    # Get info on languages and repositories.
    $settings = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/backend/youcap-info.json"), true);

    # Validate Google User ID
    $payload = validateGoogleIDToken($id);
    if(!$payload)
    {
        http_response_code(403);
        echo "403";
        return;
    }
    else
    {
        $id = $payload['sub'];
    }
        
    # Find the language and get the highest repository number. This is used to deal with repo size limits.
    $repoNum = 0;
    foreach($settings["languages"] as $languageOBJ)
    {
        if(strtolower($languageOBJ["name"]) == $language)
        {
            $repoNum = $languageOBJ["max-review-repo-number"];
        }
    }

    # Create the submission object to upload to Github (after JSON encoding).
    $submit = array(
        "author" => $user,
        "language" => $language,
        "contents" => $captionContent,
        "nsfw" => $nsfw
    );

    # Committed by the YouCap website.
    $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');

    # Uploads the JSON encoded content to the review repository.
    $fileInfo = $client->api('repo')->contents()->create('YouCap', "captions-$language-$repoNum", $path, json_encode($submit), "Committed by YouCap website", "main", $committer);
    

    # Get the SQL connection from creds.php
    $conn = mysqliConnection();
    
    # Save the important info.
    $language = mysqli_real_escape_string($conn, $language);
    $vidID = mysqli_real_escape_string($conn, $vidID);
    $id = mysqli_real_escape_string($conn, $id);
    $nsfw = mysqli_real_escape_string($conn, $nsfw);
    $sha = $fileInfo["content"]["sha"];

    # Construct the SQL and submit.
    $sql = "INSERT INTO `$sqlReviewDatabase`(`vidID`, `repoID`, `language`, `rating`, `users`, `sha`, `filters`) VALUES ('$vidID', $repoNum, '$language', 0, '$id', '$sha', 'nsfw=$nsfw')";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

?>
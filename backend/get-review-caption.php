<?php
    # sql.php includes creds.php
    require(__DIR__ . '/sql.php');
    require(__DIR__ . '/csrf-handler.php');
    require(__DIR__ . '/utils.php');

    if(!isset($_SESSION))
        session_start();

    if(!isset($csrfOverride) && !verify_csrf($_GET))
    {
        http_response_code(403);
        header("Location: /pages/errors/403.php");
        return;
    }

    # Get the ID of a random caption to review
    function chooseID($language, $user, $filters)
    {        
        # Ensure the signed in user is valid
        $payload = validateGoogleIDToken($user);
        if(!$payload)
        {
            echo "403";
            http_response_code(403);
            return;
        }
        
        # Get the user token
        $user = $payload['sub'];
        
        # Get a random caption to review.
        $result = getReviewCaption($language, $user, $filters["nsfw"]);
        $row = mysqli_fetch_array($result);
                        
        # Either return a randomly selected caption
        if(sizeof($row) > 0)
        {
            return array($row["vidID"], $row["repoID"]);
        }
        
        # If no captions meet the defined parameters
        return ["-1", "-1"];
    }

    # Variables
    $vidID = "";
    $repoID = 0;
    $vidInfo = null;

    # If the user isn't already reviewing a video. This is meant to ensure the user doesn't just keep refreshing until they get the caption they want to review.
    if(!isset($_SESSION["cache-review-id"]) || $_SESSION["cache-review-id"] == "" || $_SESSION["cache-review-id"] == "-1")
    {
        # If there's a signed in user, this is a request for captions to review from the actual page itself. (The PHP file has been included into the page)
        if(isset($user))
            # Gets the video ID of a random video that needs to be reviewed
            $vidInfo = chooseID(strtolower($_POST['vid-lang-name']), $user, array(
                "nsfw" => isset($_POST["nsfw"]) ? $_POST["nsfw"] : "false"
            ));
        # If this is a post request, it's coming from the review options page to the review studio
        if(isset($_POST["vid-lang-name"]))
            # Select a random ID
            $vidInfo = chooseID(strtolower($_POST['vid-lang-name']), $_POST["user"], array(
                "nsfw" => isset($_POST["nsfw"]) ? $_POST["nsfw"] : "false"
            ));
        # Otherwise, this is a request for captions from some external source
        else
            $vidInfo = chooseID(strtolower($_GET['vid-lang-name']), $_GET["user"], array(
                "nsfw" => isset($_GET["nsfw"]) ? $_GET["nsfw"] : "false"
            ));
                        
        # Get the video ID and repository ID from the video info
        $vidID = $vidInfo[0];
        $repoID = $vidInfo[1];
                
        # Set the session info to prevent from abuse-mitigation evasion refreshing tactic described above
        $_SESSION["cache-review-id"] = htmlspecialchars($vidID);
        $_SESSION["cache-review-repo-id"] = $repoID;
    }
    else
    {
        # If the session info exists, just use it
        $vidID = $_SESSION["cache-review-id"];
        $repoID = $_SESSION["cache-review-repo-id"];
    }

    # If this is a GET request, check if the request wanted it printed
    if(isset($_GET))
        $print = isset($_GET["print"]);

    # If this is a GET request that wants printing and if the video ID isn't -1, then the captions should be gotten and printed.
    if(isset($print) && $print && $vidID != "-1")
        echo getCaptions("captions-" . $_GET["vid-lang-name"] . "-$repoID", $vidID);

?>
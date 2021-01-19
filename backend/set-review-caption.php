<?php

    define("RATING_THRESHOLD", 3);

    # sql.php includes creds.php
    require(__DIR__ . '/sql.php');
    require(__DIR__ . '/csrf-handler.php');
    require(__DIR__ . '/utils.php');
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

    # Get the parameters
    $language = preg_replace('/\s+/', '_', strtolower($_POST["language"]));
    $vidID = $_POST["vidID"];
    $user = $_POST["user"];
    $id = $_POST["id"];
    $rating = $_POST["rating"];

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

    if(!is_numeric($rating) || (intval($rating) !== 1 && intval($rating) !== -1))
    {
        http_response_code(400);
        echo "Error 400: Bad Request.";
        return;
    }

    # Ensure this caption hasn't been reviewed by the user before.
    $result = getReviewCaptionByID($vidID, $language, $user);
    if(sizeof(mysqli_fetch_array($result)) > 0)
    {
        http_response_code(403);
        return;
    }

    # Update the rating
    updateReviewRating($vidID, $language, $id, $rating);
    
    # Get the row of information
    $result = getCaptionByID($vidID, $language, $id);
    $row = mysqli_fetch_array($result);

    # Ensure the row was found
    if(sizeof($row) > 0)
    {        
        # If the rating is outside the rating threshold, it's either published or destroyed
        if(abs($row["rating"]) >= RATING_THRESHOLD)
        {
            # Get the repository name
            $repo = "captions-$language-" . $row["repoID"];
            
            # Create the client and committer
            $client = githubClient();
            $committer = array('name' => 'YouCap Website', 'email' => 'youcapservice@gmail.com');
            
            # If it's positive, then add the file to published captions
            if($row["rating"] >= RATING_THRESHOLD)
            {
                # Commit the caption file.
                $content = getCaptionsFiltered($repo, $vidID);
                $fileInfo = $client->api('repo')->contents()->create('YouCap', $repo, "published/$vidID", $content, "Committed by YouCap website", "main", $committer);
            }
            
            # No matter what, the file needs to be deleted
            $fileInfo = $client->api('repo')->contents()->rm('YouCap', $repo, "review/$vidID", "Rating reached negative threshold. File removed by YouCap Website.", $row['sha'], "main", $committer);

            # Remove the DB entry
            deleteCaption($vidID, $language);
        }
        
        # Clear the saved session info, to allow for getting a new caption to review
        $_SESSION["cache-review-id"] = "-1";
    }

?>
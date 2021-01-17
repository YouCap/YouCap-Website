<?php
    require(__DIR__ . '/creds.php');

    # Get the SQL connection from creds.php
    $conn = mysqliConnection();

    # Prepared statement for adding a caption to the review DB
    $reviewEntryStmt = mysqli_prepare($conn, "INSERT INTO `$sqlReviewDatabase`(`vidID`, `repoID`, `language`, `rating`, `users`, `sha`, `filters`) VALUES (?, ?, ?, 0, ?, ?, ?)");
    mysqli_stmt_bind_param($reviewEntryStmt, "sissss", $vidID, $repoNum, $language, $id, $sha, $filters);

    # Prepared statement for getting a caption from the review DB
    $reviewSelectStmt = mysqli_prepare($conn, "SELECT `vidID` FROM `$sqlReviewDatabase` WHERE `vidID`=? AND `language`=? AND `users` LIKE '%'+?+'%'");
    mysqli_stmt_bind_param($reviewSelectStmt, "sss", $vidID, $language, $user);

    # Prepared statement for updating the rating of a caption in the review DB
    $reviewUpdateStmt = mysqli_prepare($conn, "UPDATE `$sqlReviewDatabase` SET `rating`=`rating` + ?,`users`=CONCAT(`users`, ',', ?) WHERE `vidID`=? AND `language`=?");
    mysqli_stmt_bind_param($reviewUpdateStmt, "isss", $rating, $user, $vidID, $language);

    # Prepared statement for deleting a caption from the review DB
    $reviewDeleteStmt = mysqli_prepare($conn, "DELETE FROM `$sqlReviewDatabase` WHERE `vidID`=? AND `language`=?");
    mysqli_stmt_bind_param($reviewDeleteStmt, "ss", $vidID, $language);

    # Inserts an entry into the review database
    function insertReviewEntry($_vidID, $_repoNum, $_language, $_id, $_sha, $_nsfw) {
        global $conn, $reviewEntryStmt;
        global $vidID, $repoNum, $language, $id, $sha, $filters;
        
        # Save the important info.
        $vidID = mysqli_real_escape_string($conn, $_vidID);
        $repoNum = $_repoNum;
        $language = mysqli_real_escape_string($conn, $_language);
        $id = mysqli_real_escape_string($conn, $_id);
        $sha = mysqli_real_escape_string($conn, $_sha);
        $filters = "nsfw=" . $_nsfw;

        # Construct the SQL and submit.
        mysqli_stmt_execute($reviewEntryStmt);
    }

    # Gets the caption info for a specific video ID, checking to ensure the user hasn't already accessed it
    function getReviewCaptionByID($_vidID, $_language, $_user) {
        global $conn, $reviewSelectStmt;
        global $vidID, $language, $user;
        
        #replaces whitespace with underscores after filtering
        $vidID = mysqli_real_escape_string($conn, $vidID);
        $language = preg_replace('/\s+/', '_', strtolower(mysqli_real_escape_string($conn, $language)));
        $user = mysqli_real_escape_string($conn, $user);
        
        # Get the results
        mysqli_stmt_execute($reviewSelectStmt);
        $result = mysqli_stmt_get_result($reviewSelectStmt);
        return $result;
    }

    # Gets the video ID and associated information for a random caption set to review
    function getReviewCaption($_language, $_user, $nsfw) {
        global $conn;
        global $sqlReviewDatabase;
        
        # Replaces whitespace with underscores after filtering
        $language = preg_replace('/\s+/', '_', strtolower(mysqli_real_escape_string($conn, $_language)));
        $user = mysqli_real_escape_string($conn, $_user);
        
        # Base SQL statement
        $sql = 
        "SELECT * FROM `$sqlReviewDatabase` WHERE `language`='$language' AND `users` NOT LIKE '%$user%'";
        
        # If NSFW should not be allowed, apply the filter. Otherwise it doesn't matter
        if($nsfw == "false")
            $sql = $sql . " AND `filters` LIKE '%nsfw=false'";
        
        # Select a random one
        $sql = $sql . " ORDER BY RAND() LIMIT 1";
                        
        # Return the result
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        return $result;
    }

    # Updates the rating for a caption
    function updateReviewRating($_vidID, $_language, $_user, $rating)
    {
        global $conn, $reviewUpdateStmt;
        global $vidID, $language, $user;
        
        # Escape the strings
        $vidID = mysqli_real_escape_string($conn, $_vidID);
        $language = mysqli_real_escape_string($conn, $_language);
        $user = mysqli_real_escape_string($conn, $_user);
        
        if(!is_numeric($rating) || (intval($rating) !== 1 && intval($rating) !== -1))
        {
            http_response_code(400);
            echo "Error 400: Bad Request.";
            return;
        }
        
        # Submit the result
        mysqli_stmt_execute($reviewUpdateStmt);
    }

    # Deletes a caption entry by video ID and language
    function deleteCaption($vidID, $language)
    {
        global $conn, $reviewDeleteStmt;
        global $vidID, $language;
     
        # Escape
        $vidID = mysqli_real_escape_string($conn, $_vidID);
        $language = mysqli_real_escape_string($conn, $_language);
        
        mysqli_stmt_execute($reviewDeleteStmt);
    }
?>
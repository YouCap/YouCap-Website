<?php
    if(!isset($_SESSION))
    {
        session_start();
        generate_csrf_key();
    }

    // Generates the key to use for CSRF token generation.
    function generate_csrf_key() {
        if(!isset($_SESSION["csrf_key"]))
            $_SESSION["csrf_key"] = bin2hex(random_bytes(32));
    }

    // Generates a CSRF token for a form.
    function generate_csrf($formName) {
        return hash_hmac('sha256', $formName, $_SESSION["csrf_key"]);
    }

    // Verifies that the CSRF token of a POST request from a specific form matches the expected value.
    function verify_csrf($post) {
        if(!isset($post["CSRFToken"]) || !isset($post["formName"]) || !isset($_SESSION["csrf_key"]))
            return false;
        
        return hash_equals(
            $post["CSRFToken"],                                             // Compare CSRF token from post request.
            hash_hmac('sha256', $post["formName"], $_SESSION["csrf_key"])   // Mirror generation process with form name.
        );
    }
?>
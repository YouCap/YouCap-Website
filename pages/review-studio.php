<?php
    $user = $_POST["user"];

    $csrfOverride = true;
    require($_SERVER["DOCUMENT_ROOT"] . "/backend/get-review-caption.php");
?>

<html>
    <head>        
        <!-- Meta -->
        <title>Review Caption - YouCap</title>
        <?php include($_SERVER["DOCUMENT_ROOT"] . '/php/meta.php'); ?>
        
        <!-- Imports -->
        <link href="https://fonts.googleapis.com/css2?family=Alata&family=Roboto+Condensed&family=Noto+Sans&display=swap" rel="stylesheet">
        
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="/css/reset.css">
        <link rel="stylesheet" type="text/css" href="/css/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="/css/studio-ui.css">
        <link rel="stylesheet" type="text/css" href="/css/create.css">
        <link rel="stylesheet" type="text/css" href="/css/studio.css">
        <link rel="stylesheet" type="text/css" href="/css/review.css">
    </head>
    <script>
        var vidID = "<?php echo $vidID; ?>";
    </script>
    <body>    
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/nav.php"); ?>
        
        <div id="main-content">
            <form>
                <h1 class="inline"><?php echo $_POST["vid-lang-name"] ?></h1>
                <a class="switch-language inline"><p>Switch Language</p></a>
                <br />
                <p class="title">&#10240;</p>
                <button type="button" class="basic-button" id="submit-button">Submit Review</button>
                <div class="sep"></div>
                
                <div id="options">
                    <h1 style="margin: 0 0 0 5px; vertical-align: middle;" class="standard-ui inline">Subtitles</h1>
                    
                    <div class="caption-list"></div>
                    
                    <div class="generating"></div>
                </div>
                <div id="video">
                    <div class="standard-ui flex">
                        <a class="shortcuts" style="margin-left: auto;">Keyboard Shortcuts</a>
                        <p class="inline">|</p>
                        <a class="help" href="/pages/organization/help.php" target="_blank">Help</a>
                    </div>
                    <div class="yt-video">
                        <div>
                            <img src="/images/icon.png" aria-label="The YouTube video to be edited will be placed here when loaded.">
                        </div>
                        <div id="player"></div>
                        <div class="editor-captions"></div>
                    </div>
                </div>
            </form>
        </div>
        
        <div id="shortcuts">
            <p>Shortcuts</p>
            <a class="close">Close</a>
            <div class="sep"></div>
            <ul>
                <li><p><span class="key">&lt;Shift&gt;</span> + <span class="key">&lt;Left&gt;</span></p></li>
                <li><p><span class="key">&lt;Shift&gt;</span> + <span class="key">&lt;Right&gt;</span></p></li>
                <li><p><span class="key">&lt;Shift&gt;</span> + <span class="key">&lt;Space&gt;</span></p></li>
            </ul>
            <ul>
                <li><p>:Seek backwards 1 second</p></li>
                <li><p>:Seek forwards 1 second</p></li>
                <li><p>:Play/Pause Video</p></li>
            </ul>
        </div>
        
        <div id="overlay">
            <div class="popup switch-language">
                <h2>Switch Language</h2>
                <p>Select a language to translate to</p>
                <p style="font-weight: bold">Select language:</p>
                <button style="margin-bottom: 10px; width: 200px;" type="button" class="select basic-select standard-ui" name="vid-lang">
                    <p class="arrow">Select Language</p>
                    <div style="width: 100%;">
                        <?php
                                //Get JSON settings and offer all languages as an option.
                                $settings = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/backend/youcap-info.json"), true);
        
                                foreach($settings["languages"] as $languageOBJ)
                                {
                                    $name = $languageOBJ["name"];
                                    $code = $languageOBJ["code"];
                                   echo "<div value='$code'><p>$name</p></div>";
                                }
                            ?>
                    </div>
                </button>
                <div class="buttons">
                    <button type="button" class="cancel basic-button">Cancel</button>
                    <button type="button" class="submit basic-button">Change Language</button>
                </div>
            </div>
            <div class="popup submission">
                <h2>Finish Review</h2>
                <p>Having watched the video, click either the accept or reject buttons below. Altenatively, you can keep watching if you're still not sure. Read our <a href="/pages/organization/help#standards" target="_blank">standards policy</a> if you're having trouble deciding whether to approve this submission or not.</p>
                <div class="buttons">
                    <button type="button" class="cancel basic-button">Keep Watching</button>
                    <button type="button" class="reject submit basic-button">Reject</button>
                    <button type="button" class="accept submit basic-button">Accept</button>
                </div>
                <form style="display: none;" method="post">
                    <input type="hidden" value="<?php echo "subtitle-review-form-" . $vidID . "-" . strtolower($_POST["vid-lang-name"]); ?>" name="formName">
                    <input type="hidden" name="CSRFToken" value="<?php echo generate_csrf("subtitle-review-form-" . $vidID . "-" . strtolower($_POST["vid-lang-name"])); ?>">
                    <input type="hidden" name="vidID" value="<?php echo $vidID; ?>">
                    <input type="hidden" name="user">
                    <input type="hidden" name="id">
                    <input type="hidden" name="language" value="<?php echo $_POST["vid-lang-name"]; ?>">
                    <input type="hidden" name="rating">
                </form>
            </div>
            <div class="popup google-signin">
                <h2>Sign-in Required</h2>
                <p>To mitigate abuse, YouCap requires all captioners to sign-in to their Google accounts.</p>
                <p>Since this uses Google's OAuth 2.0 library, you can rest assured that your information is protected. <a href="/pages/organization/privacy.php" target="_blank">Learn more here</a>.</p>
                <div class="g-signin2" data-onsuccess="onSignIn"></div>
            </div>
            <div class="popup not-available">
                <h2>No Captions Available</h2>
                <p>There are currently no captions available for review.</p>
                <p>This can be due to a number of reasons, including your having reviewed all available captions and no captions meeting your specified filters.</p>
                <p>You can help by exiting back to the options page and expanding your filters, or by contributing new captions to YouCap.</p>
                <div class="buttons">
                    <a href="/pages/review" style="margin-right: 10px;"><button type="button" class="basic-button">Exit to Options</button></a>
                    <a href="/pages/create"><button type="button" class="submit basic-button">Create</button></a>
                </div>
            </div>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
    
    <script>
        var langName, vidLangName;
        langName = vidLangName = "<?php echo $_POST['vid-lang-name']; ?>";
        var vidLang = "<?php echo $_POST["vid-lang"]; ?>";
        var nsfw = "<?php echo isset($_POST["nsfw"]) ? "true" : "false"; ?>";
    </script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
    
    <script>
        var CapCSRF = "<?php echo generate_csrf("captions"); ?>";
    </script>
    <script src="/js/captions.js"></script>
    <script src="/js/review.js"></script>
        
    <script src="/js/github-utils.js"></script>
    <script src="/js/google-utils.js"></script>
    <script>
        onSignedIn = function() {
            $(".popup.google-signin").removeClass("show");
            
            if($("#overlay .popup.show").length <= 0)
                $("#overlay").removeClass("show");
            
            $(".popup.submission input[name=name]").val(profile.getName());
            $(".popup.submission input[name=email]").val(profile.getEmail());
        };
        
        onNotSignedIn = function() {
            $(".popup.google-signin, #overlay").addClass("show");
        }
        
        if(vidID == "-1")
            $("#overlay, #overlay .popup.not-available").addClass("show");
    </script>
    <script>
        var vidLang = $_POST["vid-lang"];
        var vidLangName = $_POST["vid-lang-name"];
        var nsfw = $_POST["nsfw"];
    </script>
</html>
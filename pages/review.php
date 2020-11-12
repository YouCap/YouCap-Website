<html>
    <head>        
        <!-- Meta -->
        <title>Create Caption - YouCap</title>
        <?php include($_SERVER["DOCUMENT_ROOT"] . '/php/meta.php'); ?>
        
        <!-- Imports -->
        <link href="https://fonts.googleapis.com/css2?family=Alata&family=Roboto+Condensed&family=Noto+Sans&display=swap" rel="stylesheet">
        
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="/css/reset.css">
        <link rel="stylesheet" type="text/css" href="/css/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="/css/studio-ui.css">
        <link rel="stylesheet" type="text/css" href="/css/create.css">
        <link rel="stylesheet" type="text/css" href="/css/studio.css">
        <link rel="stylesheet" type="text/css" href="/css/review-studio.css">
    </head>
    <body>    
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/nav.php"); ?>
        
        <div id="main-content">
            <form action="/pages/review-studio.php" method="post">
                <div id="options" style="margin: auto;">
                    <h1 class="standard-ui">Link to Video</h1>
                    <div class="sep"></div>
                    
                    <label for="vid-lang">Caption Language</label>
                    <button style="margin-bottom: 10px;" type="button" class="select" name="vid-lang">
                        <p class="arrow">Select Language</p>
                        <div>
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
                    <input type="hidden" name="vid-lang">
                    <input type="hidden" name="vid-lang-name">
                    <input type="hidden" name="user">
                    <div class="sep"></div>
                    
                    
                    <button type="submit">Start Reviewing</button>
                </div>
                <div id="video">
                    <h1 class="standard-ui">Options</h1>
                    <div class="sep"></div>
                    
                    <div class="options">
                        <div class="checkbox">
                            <input type="checkbox" name="nsfw">
                            <span class="checkmark"><img src="/images/page-icons/checkbox.png"></span>
                        </div>
                        <p>NSFW</p>
                        <br>
                        <div class="empty-space">
                            <p>Where are all the options?</p>
                            <div class="popup">
                                <p>As YouCap gets a wider selection of captions, we'll be able to expand filters while keeping our abuse mitigation system intact. To help, find your favorite video and make a caption for it!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="overlay" class="">
            <div class="popup google-signin">
                <h2>Sign-in Required</h2>
                <p>To mitigate abuse, YouCap requires all reviewers to sign-in to their Google accounts.</p>
                <p>Since this uses Google's OAuth 2.0 library, you can rest assured that your information is protected. <a href="/pages/privacy.php">Learn more here</a>.</p>
                <div class="g-signin2" data-onsuccess="onSignIn"></div>
            </div>
            <div class="popup nsfw">
                <h2>NSFW Selected</h2>
                <p>Note: You have selected NSFW as a filter. This means that you may receive explicit videos to review captions for.</p>
                <p>By clicking below, you agree that you are above 13-years old and are willing to view this content. If this does not apply, hit "Cancel" and de-select the option. <a href="/pages/privacy.php#coppa">Learn more here</a>.</p>
                <div class="buttons">
                    <button type="button" class="basic-button cancel">Cancel</button>
                    <button type="button" class="submit basic-button submit">Agree</button>
                </div>
            </div>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/js/google-utils.js"></script>
    <script src="/js/studio-ui.js"></script>
    <script src="/js/create.js"></script>
    <script src="/js/review-options.js"></script>
    
    <script>
        onSignedIn = function() {
            $(".popup.google-signin, #overlay").removeClass("show");
            $("input[name=user]").val(profile.getId());
        };
        
        onNotSignedIn = function() {
            $(".popup.google-signin, #overlay").addClass("show");
        }
    </script>
</html>
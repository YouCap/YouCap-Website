<?php require($_SERVER["DOCUMENT_ROOT"] . "/backend/csrf-handler.php"); ?>
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
    </head>
    <body>    
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/nav.php"); ?>
        
        <div id="main-content">
            <form>
                <h1 class="inline"><?php echo $_GET["vid-lang-name"] ?></h1>
                <a class="switch-language inline"><p>Switch Language</p></a>
                <br />
                <p class="title">&#10240;</p>
                <button type="button" class="basic-button" id="submit-button">Submit Subtitle</button>
                <div class="sep"></div>
                
                <div id="options">
                    <button id="actions" type="button" class="basic-select select select-no-change inline" name="actions">
                        <p class="arrow">Actions</p>
                        <div>
                            <div name="auto-gen"><p>Auto-generate</p></div>
                            <div name="upload"><p>Upload a file</p></div>
                            <div name="download"><p>Download</p></div>
                        </div>
                    </button>
                    <h1 style="margin: 0 0 0 5px; vertical-align: middle;" class="standard-ui inline">Subtitle Editor</h1>
                    
                    <div class="sep"></div>
                    
                    <textarea class="add-caption" placeholder="Type subtitle then press Enter"></textarea>
                    <button class="add-caption" type="button">+</button>
                    
                    <div class="caption-list">
                    </div>
                    
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
                    <div class="waveform">
                        <canvas width=1700 height=100></canvas>
                        <div class="caption-boxes"></div>
                        <div class="playhead"></div>
                        <div class="scroll">
                            <div class="scrollbar"></div>
                        </div>
                    </div>
                    <div class="options">
                        <div class="checkbox">
                            <input type="checkbox" checked="checked">
                            <span class="checkmark"><img src="/images/page-icons/checkbox.png"></span>
                        </div>
                        <p>Pause video while typing</p>
                        <img src="/images/page-icons/magnifying-glass.png" height=20>
                        <input type="range" min=0 max=7 value=7 step=1>
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
                <li><p><span class="key">&lt;Shift&gt;</span> + <span class="key">&lt;Enter&gt;</span></p></li>
                <li><p><span class="key">&lt;Shift&gt;</span> + <span class="key">&lt;Down&gt;</span></p></li>
                <li><p><span class="key">&lt;Shift&gt;</span> + <span class="key">&lt;Up&gt;</span></p></li>
                <li><p><span class="key">&lt;Enter&gt;</span></p></li>
            </ul>
            <ul>
                <li><p>:Seek backwards 1 second</p></li>
                <li><p>:Seek forwards 1 second</p></li>
                <li><p>:Play/Pause Video</p></li>
                <li><p>:Create a new line</p></li>
                <li><p>:Move to the next subtitle</p></li>
                <li><p>:Move to the previous subtitle</p></li>
                <li><p>:Insert the subtitle</p></li>
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
            <div class="popup load-file">
                <h2>Upload a file</h2>
                <p>Upload a timed text or subtitle file. <a>Learn More</a></p>
                <p class="warning">Uploading a file will overwrite any contents.</p>
                <input type="file" accept=".srt,.sbv,.mpsub,.lrc,.cap,.vtt">
                <div class="buttons">
                    <button type="button" class="cancel basic-button">Cancel</button>
                    <button type="button" class="submit basic-button">Upload</button>
                </div>
            </div>
            <div class="popup submission">
                <h2>Submit subtitles?</h2>
                <p>Only submit the captions if you've finished working on them.</p>
                <p>To save your progress and return later, download the file through the Actions menu. Then you can upload the file at a later point in time and continue working.</p>
                <p>Additionally, to maintain a positive experience for all users, please take a moment to mark below whether this video contains any explicit or "not safe for work" content. You can read more about it by <a href="/pages/organization/privacy.php#coppa">clicking here.</a></p>
                <div class="checkbox standard-ui" name="nsfw-check" style="margin-left: 10px;">
                    <input type="checkbox">
                    <span class="checkmark"><img src="/images/page-icons/checkbox.png"></span>
                </div>
                <p style="display: inline; line-height: 17px; vertical-align: top;">Contains Explicit Content</p>
                <div class="buttons">
                    <button type="button" class="cancel basic-button">Cancel</button>
                    <button type="button" class="submit basic-button">Upload</button>
                </div>
                <form style="display: none;" action="/backend/create-file.php" method="post">
                    <input type="hidden" value="<?php echo "subtitle-creation-form-" . $_GET["vid-id"] . "-" . strtolower($_GET["vid-lang-name"]); ?>" name="formName">
                    <input type="hidden" name="CSRFToken" value="<?php echo generate_csrf("subtitle-creation-form-" . $_GET["vid-id"] . "-" . strtolower($_GET["vid-lang-name"])); ?>">
                    <input type="hidden" name="fileName">
                    <input type="hidden" name="content">
                    <input type="hidden" name="user">
                    <input type="hidden" name="email">
                    <input type="hidden" name="vid-lang-name" value="<?php echo strtolower($_GET["vid-lang-name"]) ?>">
                    <input type="hidden" name="nsfw">
                </form>
            </div>
            <div class="popup captions-exist">
                <h2>Subtitles Already Exist</h2>
                <p>There are currently subtitles that exist for this video in this language.</p>
                <p>These subtitles may not be available at the moment, as they're still in the review phase.</p>
                <p>You can help speed along this process by reviewing subtitles. (Note that subtitles are given to reviewers randomly to mitigate abuse)</p>
                <div class="buttons">
                    <a href="/" style="margin-right: 10px;"><button type="button" class="basic-button">Exit</button></a>
                    <a href="/pages/review"><button type="button" class="submit basic-button">Review</button></a>
                </div>
            </div>
            <div class="popup google-signin">
                <h2>Sign-in Required</h2>
                <p>To mitigate abuse, YouCap requires all captioners to sign-in to their Google accounts.</p>
                <p>Since this uses Google's OAuth 2.0 library, you can rest assured that your information is protected. <a href="/pages/organization/privacy.php" target="_blank">Learn more here</a>.</p>
                <div class="g-signin2" data-onsuccess="onSignIn"></div>
            </div>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
    
    <script>
        var vidID = '<?php print $_GET["vid-id"] ?>';
    </script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
    
    <script src="/js/studio.js"></script>
    <script src="/js/captions.js"></script>
    <script src="/js/studio-ui.js"></script>
    
    <script src="/js/FileSaver.min.js"></script>
    <script src="/js/cap-file-handler.js"></script>
    
    <script src="/js/github-utils.js"></script>
    <script src="/js/google-utils.js"></script>
    <script>
        onSignedIn = function() {
            $(".popup.google-signin").removeClass("show");
            
            if($("#overlay .popup.show").length <= 0)
                $("#overlay").removeClass("show");
            
            $(".popup.submission input[name=user]").val(profile.getEmail());
        };
        
        onNotSignedIn = function() {
            $(".popup.google-signin, #overlay").addClass("show");
        }
        
        fileExists("<?php echo $_GET['vid-id']; ?>", "<?php echo $_GET['vid-lang-name']; ?>", function(result) {
            if(result != "none")
                $("#overlay, #overlay .popup.captions-exist").addClass("show");
        });
    </script>
</html>
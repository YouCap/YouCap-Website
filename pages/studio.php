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
            <form action="/pages/studio.php" method="get">
                <h1 class="inline"><?php echo $_GET["vid-lang-name"] ?></h1>
                <a class="switch-language inline" href="/pages/create.php?vid-id=<?php echo $_GET["vid-id"] ?>"><p>Switch Language</p></a>
                <p class="title"></p>
                <div class="sep"></div>
                
                <div id="options">
                    <button id="actions" type="button" class="basic-select select inline" name="actions">
                        <p class="arrow">Actions</p>
                        <div>
                            <div value="auto-gen"><p>Auto-generate</p></div>
                            <div value="upload"><p>Upload a file</p></div>
                            <div value="download"><p>Download</p></div>
                        </div>
                    </button>
                    <h1 style="margin: 0 0 0 5px; vertical-align: middle;" class="standard-ui inline">Subtitle Editor</h1>
                    
                    <div class="sep"></div>
                    
                    <textarea class="add-caption" placeholder="Type subtitle then press Enter"></textarea>
                    <button class="add-caption" type="button">+</button>
                    
                    <div class="caption-list">
                    </div>
                </div>
                <div id="video">
                    <div class="standard-ui flex">
                        <a class="shortcuts" style="margin-left: auto;">Keyboard Shortcuts</a>
                        <p class="inline">|</p>
                        <a class="help" href="/pages/help.php" target="_blank">Help</a>
                    </div>
                    <div class="yt-video">
                        <div>
                            <img src="/images/icon.png" aria-label="The YouTube video to be edited will be placed here when loaded.">
                        </div>
                        <div id="player"></div>
                    </div>
                    <div class="waveform">
                        <canvas width=1700 height=100></canvas>
                        
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
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
    <script>
        var vidID = '<?php print $_GET["vid-id"] ?>';
    </script>
    <script src="/js/studio.js"></script>
    <script src="/js/captions.js"></script>
    <script src="/js/studio-ui.js"></script>
</html>
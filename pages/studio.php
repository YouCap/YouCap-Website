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
                <a class="switch-language inline"><p>Switch Language</p></a>
                <p class="title"></p>
                <div class="sep"></div>
                
                <div id="options">                    
                    <label for="vid-lang">Caption/Subtitle Language</label>
                    <button type="button" class="select" name="vid-lang">
                        <p class="arrow">Select Language</p>
                        <div>
                            <div value="en"><p>English</p></div>
                            <div value="es"><p>Spanish</p></div>
                            <div value="fr"><p>French</p></div>
                        </div>
                    </button>
                    <input type="hidden" name="vid-lang">
                    <div class="sep"></div>
                    
                    
                    <button type="submit">Start Captioning</button>
                </div>
                <div id="video">
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
                </div>
            </form>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
    <script>
        var vidID = '<?php print $_GET["vid-id"] ?>';
    </script>
    <script src="/js/studio.js"></script>
</html>
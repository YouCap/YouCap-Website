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
    </head>
    <body>    
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/nav.php"); ?>
        
        <div id="main-content">
            <form action="/pages/studio.php" onsubmit="return checkCreateForm();" method="get">
                <div id="options">
                    <label for="vid-link">Link to Video</label>
                    <input class="vid-link" type="text" placeholder="Youtube link">
                    <input name="vid-id" type="hidden">
                    
                    <button type="button" name="vid-link-button"><p>Show Video</p></button>
                    
                    <div class="sep"></div>
                    
                    <label for="vid-lang">Caption/Subtitle Language</label>
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
                    <div class="sep"></div>
                    
                    
                    <button type="submit">Start Captioning</button>
                </div>
                <div id="video">
                    <div class="yt-video">
                        <div>
                            <img src="/images/icon.png" aria-label="The YouTube video to be edited will be placed here when loaded.">
                        </div>
                        <iframe src="" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </form>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/js/create.js"></script>
    <script src="/js/studio-ui.js"></script>
</html>
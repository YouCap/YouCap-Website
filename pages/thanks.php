<html>
    <head>
        <!-- Meta -->
        <title>Thanks - YouCap</title>
        <?php include($_SERVER["DOCUMENT_ROOT"] . '/php/meta.php'); ?>
        
        <!-- Imports -->
        <link href="https://fonts.googleapis.com/css2?family=Alata&family=Roboto+Condensed&family=Noto+Sans&display=swap" rel="stylesheet">
        
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="/css/reset.css">
        <link rel="stylesheet" type="text/css" href="/css/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="/css/studio-ui.css">
        <link rel="stylesheet" type="text/css" href="/css/thanks.css">
    </head>
    <body>    
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/nav.php"); ?>
        
        <div id="main-content">
            <div class="bg"></div>
            <form action='/pages/review-studio.php' method='post'>
                <h1 class="standard-ui">Thanks for Contributing!</h1>
                <p>You're contributions are supporting those who rely on subtitles and captions to watch YouTube.</p>
                
                <input type="hidden" name="user">
                <input type="hidden" name="vid-lang" value="<?php if(isset($_GET["vid-lang"])) echo $_GET["vid-lang"]; ?>">
                <input type="hidden" name="vid-lang-name" value="<?php if(isset($_GET["vid-lang-name"])) echo $_GET["vid-lang-name"]; ?>">
                <input type="hidden" name="nsfw" value="<?php echo isset($_GET["nsfw"]); ?>">

                <div class="buttons">
                    <?php
                        if(isset($_GET["review"]))
                            echo "<button type='submit'>Review Another</button>";
                        else if(isset($_GET["create"]))
                            echo "<a href='/pages/create'>Create More</a>";
                    ?>
                    <a href="/">Go Home</a>
                </div>
            </form>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/js/google-utils.js"></script>
    <script>
        onSignedIn = function() {
            $("form input[name=user]").val(token);
        };
    </script>
</html>
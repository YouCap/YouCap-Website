<html>
    <head>
        <!-- Meta -->
        <title>Help - YouCap</title>
        <?php include($_SERVER["DOCUMENT_ROOT"] . '/php/meta.php'); ?>
        
        <!-- Imports -->
        <link href="https://fonts.googleapis.com/css2?family=Alata&family=Roboto+Condensed&family=Noto+Sans&display=swap" rel="stylesheet">
        
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="/css/reset.css">
        <link rel="stylesheet" type="text/css" href="/css/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="/css/studio-ui.css">
        <link rel="stylesheet" type="text/css" href="/css/org.css">
    </head>
    <body>    
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/nav.php"); ?>
        
        <div id="main-content">
            <div class="header" style="background-image: url('/images/hd/jametlene-reskp-gVfGGb62Fpo-unsplash.jpg');">
                <h1>Help</h1>
            </div>
            
            <h2>Creating Captions</h2>
            <p>The caption creation method was made to mirror YouTube's community caption system as much as possible. As a result, it's relatively easy to create captions for your favorite video. Follow the steps below to get started:</p>
            <ul>
                <li><p>1. Go to the <a href="/pages/create" target="_blank">create page.</a></p></li>
                <li><p>2. Enter the link to the video you want to caption and select a language to create captions for. Press "Start Captioning".</p></li>
                <li><p>3. The user interface presented gives you all you need to create high-quality captions. You can either start from scratch or get YouTube's auto-generated captions as a baseline by clicking Actions > Auto-generate.</p></li>
                <li><p>4. To add captions, you can either enter the text into the box and click the add button, or hit the insert button of a caption to create an empty caption box after it. The timeline below the video preview can be used to change the playhead position or adjust caption start and end times.</p></li>
                <li><p>5. When you're all done, hit "Submit Caption", mark whether the video is considered NSFW, and click "Submit".</p></li>
            </ul>
            <p>Following those steps will create a caption file and enter it into YouCap's review system.</p>
            
            <h2>Reviewing Captions</h2>
            <p>Caption reviewing is a system that's unique to YouCap to help mitigate platform abuse. Reviewing captions is as easy as watching a video with captions turned on, by following these simple steps:</p>
            <ul>
                <li><p>1. Go to the <a href="/pages/review" target="_blank">review page.</a></p></li>
                <li><p>2. Choose a language that you're able to understand and select any options that you want to have apply. Press "Start Reviewing".</p></li>
                <li><p>3. A video will be randomly selected for you from those that need to be reviewed. On the left-hand side, you'll be presented with a list of all of the captions for the video and the time at which they appear on screen. Hit play to start the video.</p></li>
                <li><p>4. Once you've watched the whole video, a dialog should display asking whether you want to accept or reject the captions (if they don't hit the "Submit Review" button). Decide whether or not the captions meet YouCap standards, <a href="#standards">as outlined below</a> and click the appropriate button.</p></li>
            </ul>
            <p>That's it! You've now reviewed a caption, contributing to the YouCap community!</p>
            
            <a name="standards" class="anchor"></a>
            <h2>Caption Standards</h2>
            <p>To ensure that all viewers have a positive experience while watching YouTube, our review system exists to prevent inappropriate or poor-quality captions from being viewed by users. In reviewing captions, you should take the following guidelines into account:</p>
            <ul>
                <li><p>1. They must be accurate. The occasional wrong word is not grounds for an immediate rejection, but a large amount of inaccuracies can cause a user to misunderstand a video. Grammar, while important, may be treated a bit more leniently, so long as it does not impede on the viewers ability to properly comprehend the video's subject.</p></li>
                <li><p>2. Captions must be of appropriate length per time frame. You can't provide a paragraph of information and expect a user to be able to read it effectively, and you can't flash a caption for one second and still have a viewer able to fully read it. Captions must be long enough for a viewer to read without grossly misrepresenting when a speaker is communicating.</p></li>
                <li><p>3. Captions must indicate the speaker when it is unclear. If a speaker is not shown on screen, and the captions are not carrying over from a previous indication of the speaker, then it must be clarified in the text.</p></li>
                <li><p>4. Slurs must be censored. Any slurs, whether or not they're censored in the video, must be censored in any captions. This can be done by writing the first letter of the word, followed by astericks or special characters the bring the censored word up to the same length as the original. For a full list of slurs and inappropriate words, see this <a href="https://en.wikipedia.org/wiki/Lists_of_pejorative_terms_for_people" target="_blank">Wikipedia article</a>. Note that certain words that appear in the list are only considered slurs in the correct context.</p></li>
            </ul>
            <p>By following these standards, the YouCap community can continue to ensure that users of the service continue to hold free access to high-quality YouTube captions.</p>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
</html>
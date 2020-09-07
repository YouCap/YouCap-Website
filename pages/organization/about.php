<html>
    <head>
        <!-- Meta -->
        <title>About YouCap</title>
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
            <div class="header" style="background-image: url('/images/hd/szabo-viktor-4QmSdCP4bhM-unsplash.jpg');">
                <h1>What's YouCap?</h1>
            </div>
            
            <h2>YouCap's Mission</h2>
            <p>On July 30th, 2020, YouTube announced that they were discontinuing the platform's community captions feature, despite many relying on the multilingual feature to be able to watch videos. That's where YouCap comes in.</p>
            <p>YouCap is an open-source captioning solution to support community-driven captioning for the YouTube platform. It works by collecting and hosting user-created captions, before offering them to YouTube viewers who install the YouCap extension. The best part? It is and will always be 100% free. We're funded exclusively by donations (that means no ads either).</p>
            <p>Our goal is to create captions for as many videos as possible. Want to join us? Click below to get started.</p>
            <a href="/pages/create.php"><p>Start Captioning</p></a>
            
            <h2>Contributing to YouCap</h2>
            <p>The best way to help contribute to YouCap's mission is to help create or review captions. However, we recognize that this may not be everyone's forte. Fear not, for there's plenty of ways to help us out!</p>
            <ul>
                <li><p>1. Volunteer: Maintaining the YouCap service is hard work, and it doesn't come easy. We're always looking for people who are willing to contribute their time. There are a few ways you can volunteer. For one, caption repository managers are always needed. Your job would be to simply go through our existing captions and point out any that might need some work. Additionally, our website and extensions can always use some work. Programmers, especially those with knowledge of HTML, CSS, JavaScript, and PHP, can check out <a href="https://www.github.com/YouCap" target="_blank">our Github page</a> and contribute code.</p></li>
                <li><p>2. Security Researchers: As much as we'd like to think that our code is impervious to attack, we understand that that's usually not the case. As such, we've outlined <a href="/pages/organization/security.php">a security policy</a>, including our vulnerability disclosure program, to describe how security researchers can safely and efficiently submit vulnerabilities to YouCap.</p></li>
                <li><p>3. Other projects/ideas: Creativity is a beautiful thing, and we love to work with our community on cool ideas. If you want to talk about a way to support YouCap's mission that you don't see listed here, feel free to contact us.</p></li>
            </ul>
            
            <h2>More About YouCap</h2>
            <ul>
                <li><p>1. Check out <a href="/pages/organization/help.php">our guide</a> on creating and reviewing captions.</p></li>
                <li><p>2. Read up on <a href="/pages/organization/privacy.php">our commitment to user privacy</a>.</p></li>
                <li><p>3. Learn about <a href="/pages/organization/security">our security policy</a>.</p></li>
                <li><p>4. See <a href="/pages/organization/press">what we've got</a> for members of the media and those seeking to use YouCap branding materials.</p></li>
            </ul>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
</html>
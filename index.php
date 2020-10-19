<html>
    <head>
        <!-- Meta -->
        <title>YouCap</title>
        <?php include($_SERVER["DOCUMENT_ROOT"] . '/php/meta.php'); ?>
        
        <!-- Imports -->
        <link href="https://fonts.googleapis.com/css2?family=Alata&family=Roboto+Condensed&family=Noto+Sans&display=swap" rel="stylesheet">
        
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="/css/reset.css">
        <link rel="stylesheet" type="text/css" href="/css/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="/css/homepage.css">
    </head>
    <body>
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/nav.php"); ?>
        
        <div id="main-content">
            <a href="/pages/create">
                <div class="selection">
                    <div class="img" style="background-image: url(/images/hd/glenn-carstens-peters-npxXWgQ33ZQ-unsplash.jpg)"></div>
                    <h2>Create</h2>
                    <p>Contribute to the community repository of YouTube captions.</p>
                </div>
            </a>
            <a href="/pages/review">
                <div class="selection">
                    <div class="img" style="background-image: url(/images/hd/aaron-burden-o-ubWHV29Uk-unsplash.jpg)"></div>
                    <h2>Review</h2>
                    <p>Approve or reject captions based on their appropriateness and accuracy.</p>
                </div>
            </a>
            <a href="/pages/organization/extension">
                <div class="selection">
                    <div class="img" style="background-image: url(/images/hd/photo-1541877944-ac82a091518a.jpg)"></div>
                    <h2>Extension</h2>
                    <p>Get the browser extension to have YouCap's captions overlaid onto YouTube videos.</p>
                </div>
            </a>
            <a href="/pages/organization/about">
                <div class="selection">
                    <div class="img" style="background-image: url(/images/hd/nordwood-themes-8LfE0Lywyak-unsplash.jpg)"></div>
                    <h2>About</h2>
                    <p>Learn more about the YouCap service, including our standards and additional ways to contribute.</p>
                </div>
            </a>
            <div class="span left">
                <img src="images/illustrations/drawkit-notebook-man-colour-800px.png" width="70%">
            </div>
            <div class="span right">
                <h2>YouCap is all about creating.</h2>
                <p>Our goal is to provide captions for as many YouTube videos as possible, in as many languages as possible. To that end, we offer a caption studio to create new captions or review those created by others.</p>
                <a href="/pages/create"><p>Start Creating</p></a>
            </div>
            <div class="sep span2"></div>
            <div class="span right">
                <img src="images/illustrations/mobile-post-colour-800px.png" width="70%">
            </div>
            <div class="span left">
                <h2>Using YouCap's captions is as simple as a single browser extension.</h2>
                <p>YouCap's website works in conjunction with a browser extension to offer a seamless experience for users. Simply install it on your device and navigate to YouTube. From there you'll have a nearly identical experience to YouTube's own caption system.</p>
                <a href="/pages/organization/extension"><p>Get the Extension</p></a>
            </div>
            <div class="sep span2"></div>
            <div class="span left">
                <img src="images/illustrations/unlock-800px.png" width="70%">
            </div>
            <div class="span right">
                <h2>Privacy and security is our number one priority. We take it seriously, because we know it's serious to you.</h2>
                <p>In an increasingly digital world, user information is constantly risking exposure. That's why YouCap operates off of a least-privilege model. That's to say, we use and store as little data as necessary to operate our platform. We also encourage security researchers to report any vulnerabilities they might find.</p>
                <a href="/pages/organization/privacy"><p>Read our Privacy Policy</p></a>
                <a href="/pages/organization/security"><p>Visit the Security Center</p></a>
            </div>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
</html>
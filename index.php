<html>
    <head>
        <!-- Meta -->
        <title>YouCap</title>
        <?php include($_SERVER["DOCUMENT_ROOT"] . '/php/meta.php'); ?>
        
        <!-- Imports -->
        <link href="https://fonts.googleapis.com/css2?family=Alata&family=Roboto+Condensed&display=swap" rel="stylesheet">
        
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
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
</html>
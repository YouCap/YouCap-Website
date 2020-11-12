<html>
    <head>
        <!-- Meta -->
        <title>Privacy - YouCap</title>
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
            <div class="header" style="background-image: url('/images/hd/morning-brew-MlHWaQBmZfU-unsplash.jpg'); background-position: bottom center;">
                <h1>Privacy</h1>
            </div>
            
            <h2>Our Privacy Commitment</h2>
            <p>YouCap takes your privacy very seriously. As a result, we minimize what data of yours we process, balancing it with the need to mitigate abuse.</p>
            <p>We use Google's sign-in system to keep track of users of our platform. Note the sign-in button in the top-right corner. This system works by authorizing the user through Google's authentication system, then relaying the account name and email back to our system (Note that we never have access to anything beyond your name, email address, Google Account ID, and profile picture). Your Google Account ID is then used to track what videos you create captions for and review, which is accomplished by temporarily storing the value in a self-hosted database. These account ID records are deleted once a set of captions have been approved or rejected from the review system. This is part of our abuse mitigation system, which you can <a href="#abuse">learn more about here</a>.</p>
            <p>Beyond that, we do not collect any of your information. No trackers, no fingerprinting, and no data collection. That is our privacy commitment to you.</p>
            
            <a class="anchor" name="abuse"></a>
            <h2>Abuse Mitigation</h2>
            <p>Our system provides captions to many YouTube users, so protecting them from harmful captions is essential.</p>
            <p>The first layer of our abuse defense is the Google sign-in button. By authenticating through Google, we guarantee that real users are engaging with our platform while providing a robust sign-in process.</p>
            <p>The second layer involves our community-driven review process. After captions are submitted, they're entered into our review system, which serves to stage captions before their release. These captions must meet a certain threshold of YouCap user approvals or rejections in our review system. If they meet the approval threshold, they're made available for YouTube viewers. If they meet the rejection threshold, they're deleted from the system.</p>
            <p>Our final layer of defense involves our caption repository managers. These managers, a team made up of verified volunteers, occasionally review submitted captions and make modifications as necessary.</p>
            
            <a class="anchor" name="coppa"></a>
            <h2>COPPA</h2>
            <p>COPPA stands for the Children's Online Privacy Protection Act. It governs the protection of children from viewing materials that could be explicit or otherwise harmful. For YouCap, that includes explicit captions. Any videos or captions that contain sexual or graphic materials must be marked as NSFW (Not Safe For Work). While we're not considered responsible for user-generated content, we do our best to keep captions appropriate for their intended audience through moderation and filtering systems.</p>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
</html>
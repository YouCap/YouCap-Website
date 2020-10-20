<html>
    <head>
        <!-- Meta -->
        <title>Security - YouCap</title>
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
            <div class="header" style="background-image: url('/images/hd/moja-msanii-vO9-gal54go-unsplash.jpg');">
                <h1>Security</h1>
            </div>
                                    
            <h2>Vulnerability Disclosure Policy</h2>
            <p>We put a lot of focus on security, it's considered one of our top priorities. Having developed with security in mind from the beginning, our system has sanitization, CSRF tokens, and GET/POST request validation. However, having started with security in mind, we also recognize that our platform can always be improved, especially from a security perspective. As a result, we allow security researchers to research and report vulnerabilities as outlined below.</p>
            <h3>Active and Passive Research</h3>
            <p>Passive research, which we define as discovery of vulnerabilities through analysis of a public code repository, is in contrast with active research, which we define as seeking out vulnerabilities through use of the code. Due to YouCap's use of third-party providers for hosting and software distribution, active research against YouCap's live software or sites is prohibited. However, researchers can download and locally host their own copy of the code if they wish to engage in active research. Otherwise, the researcher must utilize passive research.</p>
            <h3>In-scope</h3>
            <p>The following platforms are considered in scope:</p>
            <ul>
                <li><p>1. https://www.youcap.com</p></li>
                <li><p>2. https://*.youcap.com</p></li>
            </ul>
            <h3>Out-of-scope</h3>
            <p>The following platforms/services and vulnerabilities are considered out-of-scope:</p>
            <ul>
                <li><p>1. Any platform, service or software not explicitly defined in the "In-scope" section.</p></li>
                <li><p>2. Anything utilizing a third-party provider, including this live site and our Github repositories.</p></li>
                <li><p>3. DoS/DDoS/DRDoS.</p></li>
                <li><p>4. Lack of CSRF tokens, unless the lack of such token can be shown to negatively impact security.</p></li>
                <li><p>5. Physical attacks.</p></li>
                <li><p>6. Social engineering.</p></li>
                <li><p>7. Self-XSS.</p></li>
                <li><p>8. Attacks based entirely on unreasonable user behavior or prior compromise of a device.</p></li>
            </ul>
            <h3>Safe Harbor</h3>
            <p>Any activity conducted as a part of this policy will be considered authorized and legal action against any involved parties will not be intiated. Should legal action be intiated by a third-party, in connection with activities conducted under this policy, we will take action to make it known that your activities were authorized under this policy.</p>
            <h3>How to Report</h3>
            <p>To report a vulnerability, please email it to us at <a href="mailto:youcapservice@gmail.com">YouCapService@gmail.com</a>.</p>
            <p>If desired, the following PGP key may be used to encrypt files:</p>
            <pre class="code">
-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: FlowCrypt Email Encryption 7.9.0
Comment: Seamlessly send and receive encrypted email

xjMEX2wJghYJKwYBBAHaRw8BAQdAaKsbyTU507TzcHOuDRJ32GZ7S5AeZgYO
ZA5wvg37VePNIVlvdSBDYXAgPHlvdWNhcHNlcnZpY2VAZ21haWwuY29tPsKP
BBAWCgAgBQJfbAmCBgsJBwgDAgQVCAoCBBYCAQACGQECGwMCHgEAIQkQGRaR
mlTFuBEWIQSdq/yzu64J2NDQeGEZFpGaVMW4EenHAQDVzuZ/tpklMdGZSFQg
qPSYefllWBYKAuDPpMMdJsoF0QEA+vD7PW4HipYouR7V+iZYvpLMfR/ndfIf
XfbpD1cVpgPOOARfbAmCEgorBgEEAZdVAQUBAQdAKfrM3HQP9gStt8pGuVDN
sniG0onQS2a+iulXQwmbGg0DAQgHwngEGBYIAAkFAl9sCYICGwwAIQkQGRaR
mlTFuBEWIQSdq/yzu64J2NDQeGEZFpGaVMW4EcSWAP4xjigKIjDPy61XyEWa
wigFZ8sqo800K8pfb72xFQiWeQEAzaTJYUQrpWeOKuFtu5xPK+M4p4CBHCBI
G7f+fGujAgQ=
=qvTY
-----END PGP PUBLIC KEY BLOCK-----
            </pre>
            <h3>Report Standards</h3>
            <p>All vulnerability reports must include the following:</p>
            <ul>
                <li><p>1. A description of the vulnerability</p></li>
                <li><p>2. An explanation of the potential security impact of the vulnerability.</p></li>
                <li><p>3. Whether any sensitive data was accessed and, if so, what.</p></li>
                <li><p>4. A working proof of concept, if one exists.</p></li>
            </ul>
            <h3>Rewards</h3>
            <p>At this time, we do not offer monetary rewards for verified vulnerabilities. However, we do highlight security researchers through our Research Hall of Fame, shown below.</p>
            
            <h2>Researcher Hall of Fame</h2>
            <p>Below is a list of security researchers who reported verifiable vulnerabilities to YouCap.</p>
        </div>
        
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/php/footer.php"); ?>
    </body>
</html>
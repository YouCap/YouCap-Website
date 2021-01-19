<?php
    require($_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php");

    use Http\Adapter\Guzzle6\Client as GuzzleClient;
    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\Signer\Rsa\Sha256;
    use Github\HttpClient\Builder as GHBuilder;
    use Github\Client as GHClient;

    # Create a Github Client
    function githubClient() {
        $builder = new GHBuilder(new GuzzleClient());
        $github = new GHClient($builder, 'machine-man-preview');
        
        $integrationId = '77561';
        $installationId = '11338372';

        $jwt = (new Builder)
            ->setIssuer($integrationId)
            ->setIssuedAt(time())
            ->setExpiration(time() + 60)
            // `file://` prefix for file path or file contents itself
            ->sign(new Sha256(),  new Key('file://' . $_SERVER["DOCUMENT_ROOT"] . '/secrets/youcap-website.pem'))
            ->getToken();

        $github->authenticate($jwt, null, GHClient::AUTH_JWT);

        $token = $github->api('apps')->createInstallationToken($installationId);
        $github->authenticate($token['token'], null, GHClient::AUTH_ACCESS_TOKEN);
        
        return $github;
    }

    # The SQL review database
    $sqlReviewDatabase = "";

    # Create an SQL connection
    function mysqliConnection() {
        $credJSON = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/secrets/secrets.json"), true)["mysql"];

        $sqlServer = $credJSON["servername"];
        $sqlUsername = $credJSON["username"];
        $sqlPassword = $credJSON["password"];
        $sqlReviewDB = $credJSON["review_db"];
        
        global $sqlReviewDatabase;
        $sqlReviewDatabase = $credJSON["review_table"];
        
        $conn = new mysqli($sqlServer, $sqlUsername, $sqlPassword, $sqlReviewDB);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    }

    # Validates a Google User ID from the clientside according to Google specifications
    function validateGoogleIDToken($id_token) {
        # Get the client ID
        $credJSON = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/secrets/secrets.json"), true)["google"];        
        $client = new Google_Client(['client_id' => $credJSON["clientID"]]);
        
        # Validate
        return $client->verifyIdToken($id_token);
    }
?>
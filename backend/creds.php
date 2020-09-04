<?php
    use Http\Adapter\Guzzle6\Client as GuzzleClient;
    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\Signer\Rsa\Sha256;
    use Github\HttpClient\Builder as GHBuilder;
    use Github\Client as GHClient;

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

    function mysqliConnection() {
        $credJSON = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/secrets/secrets.json"), true)["mysql"];

        $sqlServer = $credJSON["servername"];
        $sqlUsername = $credJSON["username"];
        $sqlPassword = $credJSON["password"];
        
        $conn = new mysqli($sqlServer, $sqlUsername, $sqlPassword, "review_users");
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    }
?>
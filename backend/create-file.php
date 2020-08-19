<?php
    use Http\Adapter\Guzzle6\Client as GuzzleClient;
    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\Signer\Rsa\Sha256;

    $builder = new Github\HttpClient\Builder(new GuzzleClient());
    $github = new Github\Client($builder, 'machine-man-preview');

    $jwt = (new Builder)
        ->setIssuer($integrationId)
        ->setIssuedAt(time())
        ->setExpiration(time() + 60)
        // `file://` prefix for file path or file contents itself
        ->sign(new Sha256(),  new Key('file:///path/to/integration.private-key.pem'))
        ->getToken();

    $github->authenticate($jwt, null, Github\Client::AUTH_JWT);

    $token = $github->api('apps')->createInstallationToken($installationId);
    $github->authenticate($token['token'], null, Github\Client::AUTH_ACCESS_TOKEN);
?>
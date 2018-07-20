<?php
declare(strict_types=1);

namespace App\Auth\Request\Mapper;

use GuzzleHttp\Psr7\ServerRequest,
    GuzzleHttp\Psr7\LazyOpenStream;

use App\Library\Api\Request\Mapper\RequestMapperInterface,
    App\Library\Api\Request\Mapper\JsonRequestTrait,
    App\Library\Api\Request;

class Psr7RequestMapper implements RequestMapperInterface
{

    use JsonRequestTrait;

    public function mapRequest(Request $request)
    {
        $contentType = $request->getHeader('Content-Type');
        $this->validateContentType($contentType);
        $values = $this->bodyToArray($request->getRawBody());

        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $uri = $request->getURI();
        $body = new LazyOpenStream('php://input', 'r+');
        $protocol = '1.1';
        $serverRequest = new ServerRequest($method, $uri, $headers, $body, $protocol, $_SERVER);

        return $serverRequest->withParsedBody($values);
    }

}

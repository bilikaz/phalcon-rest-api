<?php
declare(strict_types=1);

namespace App\Auth\Response\Mapper;

use GuzzleHttp\Psr7\ServerResponse,
    GuzzleHttp\Psr7\LazyOpenStream;

use App\Library\Api\Response\Mapper\ResponseMapperInterface;

class Psr7ResponseMapper implements ResponseMapperInterface
{

    public function mapResponse($response)
    {
        $content = (string) $response->getBody();
        $response->getBody()->close();
        return json_decode($content, true);
    }

}

<?php
declare(strict_types=1);

namespace App\Library\Api;

use Phalcon\Mvc\Controller as PhalconController;

use App\Library\Api\Request\Mapper\RequestMapperInterface;

class Controller extends PhalconController
{

    public function getRequestMappers()
    {
        return [];
    }

    public function getResponseMappers()
    {
        return [];
    }

    public function mapRequest(string $action, RequestMapperInterface $defaultRequestMapper)
    {
        $requestMappers = $this->getRequestMappers();
        if (isset($requestMappers[$action])) {
            $requestMapper = $requestMappers[$action];
            if (is_string($requestMapper)) {
                $requestMapper = new $requestMapper();
            } else {
                $requestMapper = $requestMapper();
            }
        } else {
            $requestMapper = $defaultRequestMapper;
        }
        return $requestMapper->mapRequest($this->request);
    }

    public function mapResponse(string $action, $response)
    {
        $responseMappers = $this->getResponseMappers();
        if (isset($responseMappers[$action])) {
            $responseMapper = $responseMappers[$action];
            if (is_string($responseMapper)) {
                $responseMapper = new $responseMapper();
            } else {
                $responseMapper = $responseMapper();
            }
            $response = $responseMapper->mapResponse($response);
        }
        return $response;
    }

}

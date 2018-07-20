<?php
declare(strict_types=1);

namespace App\Library\Api\Response;

use Phalcon\Mvc\Micro as Application;

class JsonResponse implements ResponseInterface
{

    private $response;

    public function setResponse($content, int $responseCode, Application $application)
    {
        $this->response = $application->getSharedService('response');
        $this->response->setStatusCode($responseCode);
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($content);
    }

    public function send()
    {
        $this->response->send();
    }

}

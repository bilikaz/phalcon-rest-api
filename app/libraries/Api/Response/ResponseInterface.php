<?php
declare(strict_types=1);

namespace App\Library\Api\Response;

use Phalcon\Mvc\Micro as Application;

interface ResponseInterface
{

    public function setResponse($content, int $responseCode, Application $application);

    public function send();

}

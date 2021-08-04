<?php
declare(strict_types=1);

namespace App\Library\Api\EventsManager;

use Exception;

use Phalcon\Mvc\Micro as Application,
    Phalcon\Events\Event;

use App\Library\Api\Exception\ApiException,
    App\Library\Api\Exception\UrlNotFoundException,
    App\Library\Api\Response\ResponseInterface,
    App\Library\Repository\Exception\RepositoryException;

class ResponseManager
{

    private $defaultResponse;

    public function __construct(Application $application, ResponseInterface $defaultResponse)
    {
        $this->defaultResponse = $defaultResponse;
        $application->error(
            function ($exception) use ($application) {
                if (!is_a($exception, 'Exception')) {
                    $exception = new Exception($exception . '');
                }
                $this->exception($exception, $application);
            }
        );
    }

    public function beforeNotFound(Event $event, Application $application)
    {
        $this->exception(new UrlNotFoundException(), $application);
    }

    public function exception(Exception $exception, Application $application)
    {
        if ($application->hasService('db')) {
            $db = $application->getSharedService('db');
            if ($db->getTransactionLevel()) {
                $db->rollback();
            }
        }

        $config = $application->getSharedService('config');
        if ($exception instanceof ApiException) {
            $content = $exception->getResponse();
            $responseCode = $exception->getResponseCode();
            if ($exception instanceof RepositoryException && $config->application->debug) {
                $content['error']['details'] = array_merge($content['error']['details'] ? [$content['error']['details']] : [], $exception->getDetails());
            }
        } else {
            if ($config->application->debug) {
                return;
            }
            $content = [
                'error' => [
                    'code' => 5000,
                    'message' => 'Internal error',
                ],
            ];
            $responseCode = 500;
        }

        $this->sendResponse($content, $responseCode, $application);
        exit();
    }

    public function afterHandleRoute(Event $event, Application $application)
    {
        if ($application->hasService('db')) {
            $db = $application->getSharedService('db');
            if ($db->getTransactionLevel()) {
                $db->commit();
            }
        }

        $content = $application->getReturnedValue();
        if (is_object($content)) {
            if ($content instanceof ResponseInterface) {
                $content->send();
                return true;
            } elseif (method_exists($content, 'toArray')) {
                $content = $content->toArray();
            } else {
                $content = (array) $content;
            }
        }

        if (!is_array($content)) {
            $content = [
                'message' => $content,
            ];
        }
        $this->sendResponse($content, 200, $application);
        return true;
    }

    private function sendResponse(array $content, int $responseCode, Application $application)
    {
        $this->defaultResponse->setResponse($content, $responseCode, $application);
        $this->defaultResponse->send();
    }

}

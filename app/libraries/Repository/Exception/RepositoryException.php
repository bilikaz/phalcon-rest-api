<?php
declare(strict_types=1);

namespace App\Library\Repository\Exception;

use App\Library\Api\Exception\ApiException;

class RepositoryException extends ApiException
{

    protected $responseCode = 500;
    protected $errorCode = 5000;
    protected $errorMessage = 'Repository error';
    protected $model;
    protected $details;

    public function __construct($message, $model)
    {
        $this->model = $model;
        parent::__construct($message);
    }

    public function getDetails()
    {
        if (!isset($this->details)) {
            $this->details = [];
            foreach ($this->model->getMessages() as $message) {
                $this->details[] = $message->getMessage();
            }
        }
        return $this->details;
    }

}

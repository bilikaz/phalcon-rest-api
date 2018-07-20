<?php
declare(strict_types=1);

namespace App\Library\Api\Request\Mapper;

use Exception;

use App\Library\Api\Exception\IncorrectFieldsException;

class JsonRequestMapper extends AbstractRequestMapper
{

    use JsonRequestTrait;

}

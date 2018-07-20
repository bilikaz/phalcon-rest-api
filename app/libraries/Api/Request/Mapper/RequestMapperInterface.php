<?php
declare(strict_types=1);

namespace App\Library\Api\Request\Mapper;

use App\Library\Api\Request;

interface RequestMapperInterface
{

    public function mapRequest(Request $request);

}

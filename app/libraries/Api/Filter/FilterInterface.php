<?php
declare(strict_types=1);

namespace App\Library\Api\Filter;

interface FilterInterface
{

    public function filter($value, array $values);

}

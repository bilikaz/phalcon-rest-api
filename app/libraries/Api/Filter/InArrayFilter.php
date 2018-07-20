<?php
declare(strict_types=1);

namespace App\Library\Api\Filter;

use Exception;

class InArrayFilter implements FilterInterface
{

    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function filter($value, array $values)
    {
        if (in_array($value, $this->values)) {
            return $value;
        }
        throw new Exception('Value should be one of: \'' . implode('\', \'', $this->values) . '\'');
    }

}

<?php
declare(strict_types=1);

namespace App\Library\Repository;

use Phalcon\Mvc\Model as PhalconModel;

class Model extends PhalconModel
{

    private static $repositories = [];

    public function __get($name)
    {
        if (substr($name, -10) === 'Repository') {
            if (!isset(self::$repositories[$name])) {
                self::$repositories[$name] = $this->getDI()->getShared($name);
            }
            return self::$repositories[$name];
        }
        return parent::__get($name);
    }

}

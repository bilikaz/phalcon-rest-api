<?php
declare(strict_types=1);

namespace App\Library\Api\Route;

use App\Library\Api\Request\Mapper\RequestMapperInterface;

interface RouteInterface
{

    public function __construct(bool $authEnabled);

    public function getNamespace();

    public function setNamespace(string $namespace);

    public function getMask();

    public function setMask(string $mask);

    public function getMethod();

    public function setMethod(string $method);

    public function getUri();

    public function setUri(string $uri);

    public function getPrefix();

    public function setPrefix(string $prefix);

    public function getAuthEnabled();

    public function setAuthEnabled(bool $authEnabled);

    public function resolve();

    public function handle(RequestMapperInterface $defaultRequestMapper);
}

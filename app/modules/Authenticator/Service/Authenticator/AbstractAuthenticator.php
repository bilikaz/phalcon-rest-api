<?php
declare(strict_types=1);

namespace App\Authenticator\Service\Authenticator;

use stdClass;

use App\Library\Api\Config,
    App\Authenticator\Model\UserAuthenticator;

abstract class AbstractAuthenticator
{

    protected $config;
    protected $userAuthenticatorModel;

    public function __construct(Config $config, UserAuthenticator $userAuthenticatorModel)
    {
        $this->config = $config;
        $this->userAuthenticatorModel = $userAuthenticatorModel;
    }

    abstract public function validate(string $code);

    abstract public function getSetupParams();

}

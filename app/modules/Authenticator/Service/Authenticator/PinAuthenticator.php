<?php
declare(strict_types=1);

namespace App\Authenticator\Service\Authenticator;

use stdClass;

use App\Authenticator\Exception\AuthenticatorException,
    App\Authenticator\Exception\AuthenticatorSetupException;

class PinAuthenticator extends AbstractAuthenticator
{

    public function validate(string $code)
    {
        if ($this->userAuthenticatorModel->params->code != $code) {
            throw new AuthenticatorException();
        }
    }

    public function getSetupParams()
    {
        if ($this->userAuthenticatorModel->setup == 'done') {
            throw new AuthenticatorSetupException();
        }
        if ($this->userAuthenticatorModel->params) {
            return $this->userAuthenticatorModel->params;
        }
        $params = new stdClass();
        $params->code = str_pad((string) rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return $params;
    }

}

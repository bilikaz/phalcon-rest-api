<?php
declare(strict_types=1);

namespace App\Authenticator\Service\Authenticator;

use stdClass;

use Google\Authenticator\GoogleAuthenticator as Authenticator,
    Google\Authenticator\GoogleQrUrl;

use App\Authenticator\Exception\AuthenticatorException,
    App\Authenticator\Exception\AuthenticatorSetupException;

class GoogleAuthenticator extends AbstractAuthenticator
{

    protected $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';

    private function base32Chunk($binaryString, $bits)
    {
        $binaryString = chunk_split($binaryString, $bits, ' ');
        if (substr($binaryString, (strlen($binaryString)) - 1)  == ' ') {
            $binaryString = substr($binaryString, 0, strlen($binaryString)-1);
        }
        return explode(' ', $binaryString);
    }

    public function base32Encode($string)
    {
        if (strlen($string) == 0) {
            return '';
        }
        $binaryString = '';
        foreach (str_split($string) as $s) {
            $binaryString .= sprintf('%08b', ord($s));
        }
        $binaryArray = $this->base32Chunk($binaryString, 5);
        while (count($binaryArray) % 8 !== 0) {
            $binaryArray[] = null;
        }
        $base32String = '';
        foreach ($binaryArray as $bin) {
            $char = 32;
            if (!is_null($bin)) {
                $bin = str_pad($bin, 5, '0', STR_PAD_RIGHT);
                $char = bindec($bin);
            }
            $base32String .= $this->alphabet[$char];
        }
        return str_replace('=', '', $base32String);
    }

    public function validate(string $code)
    {
        $authenticator = new Authenticator();
        if (!$authenticator->checkCode($this->userAuthenticatorModel->params->code, $code)) {
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
        $params->code = $this->base32Encode(substr(md5(uniqid()), rand(0, 16), 16));
        $params->qr_url = GoogleQrUrl::generate($this->userAuthenticatorModel->user_id, $params->code, $this->config->application->name);
        return $params;
    }

}

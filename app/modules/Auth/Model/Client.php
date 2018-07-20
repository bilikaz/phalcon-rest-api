<?php
declare(strict_types=1);

namespace App\Auth\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;

use App\Library\Repository\Model;

class Client extends Model implements ClientEntityInterface
{

    public $client_id;
    public $redirect_url;
    public $title;

    public function getName()
    {
        return $this->title;
    }

    public function setName($title)
    {
        $this->title = $title;
    }

    public function getRedirectUri()
    {
        return $this->redirect_url;
    }

    public function setRedirectUri($url)
    {
        $this->redirect_url = $url;
    }

    public function getIdentifier()
    {
        return $this->client_id;
    }

    public function setIdentifier($id)
    {
        $this->client_id = $id;
    }

}

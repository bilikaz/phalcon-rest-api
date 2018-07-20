<?php
declare(strict_types=1);

namespace App\Auth\Repository;

trait ClientsRepositoryTrait
{

    public function getClientEntity($id, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $query = $this->query('client')
            ->andWhere('client.status = :status:', ['status' => 'active'])
            ->andWhere('client.client_id = :client_id:', ['client_id' => $id])
            ->limit(1);

        $clientModel = $this->getResult($query, null);
        if (!$clientModel) {
            return null;
        }
        if ($mustValidateSecret === true && !password_verify($clientSecret, $clientModel->secret)) {
            return null;
        }
        return $clientModel;
    }
}

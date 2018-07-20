<?php
declare(strict_types=1);

namespace App\User\Repository;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;

use App\Library\Repository\AbstractRepository,
    App\Auth\Repository\UsersRepositoryTrait;

class UsersRepository extends AbstractRepository implements UserRepositoryInterface
{

    use UsersRepositoryTrait;

    const MODEL = 'App\User\Model\User';
    const ALIAS = 'user';
    const ID = 'user_id';

    public function getNextId()
    {
        $dateHash = substr(date('Ymd'), 2);

        $found = false;
        do {
            $registration = $this->getLastRegistrationForDateHash($dateHash);
            if ($registration == '999999') {
                $dateHash = str_pad((int) $dateHash + 1, 6, '0', STR_PAD_LEFT);
            } else {
                if (!isset($registration)) {
                    $registration = '000000';
                } else {
                    $registration = str_pad((int) $registration + 1, 6, '0', STR_PAD_LEFT);
                }
                $found = true;
            }
        } while (!$found);
        return $dateHash . $registration;
    }

    private function getLastRegistrationForDateHash($dateHash)
    {
        $query = $this->query()
            ->andWhere('user.user_id LIKE :user_id:', ['user_id' => $dateHash . '%'])
            ->columns(['user_id' => 'MAX(user.user_id)']);

        $lastRegistration = $this->getResult($query, null);
        if ($lastRegistration->user_id) {
            return substr($lastRegistration->user_id, -6);
        }
        return null;
    }

}

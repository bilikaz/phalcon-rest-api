<?php


use Phinx\Seed\AbstractSeed;

class Init extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'client_id' => 'client',
                'client_type_id' => 'client',
                'title' => 'web / mobile application',
                'secret' => password_hash('client', PASSWORD_DEFAULT),
                'status' => 'active',
                'timestamp_created' => time(),
                'timestamp_updated' => time(),
            ],
            [
                'client_id' => '3rd_party',
                'client_type_id' => 'service',
                'title' => '3rd party application',
                'secret' => password_hash('3rd_party', PASSWORD_DEFAULT),
                'status' => 'active',
                'timestamp_created' => time(),
                'timestamp_updated' => time(),
            ],
            [
                'client_id' => 'admin',
                'client_type_id' => 'admin',
                'title' => 'admin application',
                'secret' => password_hash('admin', PASSWORD_DEFAULT),
                'status' => 'active',
                'timestamp_created' => time(),
                'timestamp_updated' => time(),
            ],
        ];

        $table = $this->table('clients');
        $table->insert($data)
            ->save();

        $data = [
            [
                'client_type_id' => 'client',
                'title' => 'web / mobile application',
            ],
            [
                'client_type_id' => 'service',
                'title' => '3rd party application',
            ],
            [
                'client_type_id' => 'admin',
                'title' => 'admin application',
            ],
        ];

        $table = $this->table('client_types');
        $table->insert($data)
            ->save();

        $data = [
            [
                'client_type_id' => 'client',
                'scope_id' => 'client',
            ],
            [
                'client_type_id' => 'service',
                'scope_id' => 'service',
            ],
            [
                'client_type_id' => 'admin',
                'scope_id' => 'admin',
            ],
        ];

        $table = $this->table('client_type_scopes');
        $table->insert($data)
            ->save();

        $data = [
            [
                'client_type_id' => 'client',
                'scope_id' => 'user',
            ],
            [
                'client_type_id' => 'client',
                'scope_id' => 'authenticated',
            ],
            [
                'client_type_id' => 'admin',
                'scope_id' => 'administrator',
            ],
            [
                'client_type_id' => 'admin',
                'scope_id' => 'support',
            ],
            [
                'client_type_id' => 'admin',
                'scope_id' => 'authenticated',
            ],
        ];

        $table = $this->table('client_type_user_scopes');
        $table->insert($data)
            ->save();

        $data = [
            [
                'authenticator_id' => 'google',
                'title' => 'Google',
            ],
            [
                'authenticator_id' => 'pin',
                'title' => 'Pin code',
            ],
        ];

        $table = $this->table('authenticators');
        $table->insert($data)
            ->save();

        $data= [
            [
                'user_id' => 'USR-171201-000000',
                'user_type_id' => 'user',
                'email' => 'user@system.local',
                'password' => password_hash('user', PASSWORD_DEFAULT),
                'status' => 'active',
                'timestamp_created' => time(),
                'timestamp_updated' => time(),
            ],
            [
                'user_id' => 'USR-000000-000000',
                'user_type_id' => 'administrator',
                'email' => 'administrator@system.local',
                'password' => password_hash('administrator', PASSWORD_DEFAULT),
                'status' => 'active',
                'timestamp_created' => time(),
                'timestamp_updated' => time(),
            ],
            [
                'user_id' => 'USR-000000-000001',
                'user_type_id' => 'support',
                'email' => 'support@system.local',
                'password' => password_hash('support', PASSWORD_DEFAULT),
                'status' => 'active',
                'timestamp_created' => time(),
                'timestamp_updated' => time(),
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)
            ->save();

        $data = [
            [
                'user_type_id' => 'user',
                'title' => 'regular system user',
            ],
            [
                'user_type_id' => 'support',
                'title' => 'support officer',
            ],
            [
                'user_type_id' => 'administrator',
                'title' => 'system administrator',
            ],
        ];

        $table = $this->table('user_types');
        $table->insert($data)
            ->save();

        $data = [
            [
                'user_type_id' => 'user',
                'scope_id' => 'user',
            ],
            [
                'user_type_id' => 'user',
                'scope_id' => 'authenticated',
            ],
            [
                'user_type_id' => 'support',
                'scope_id' => 'support',
            ],
            [
                'user_type_id' => 'support',
                'scope_id' => 'user',
            ],
            [
                'user_type_id' => 'support',
                'scope_id' => 'authenticated',
            ],
            [
                'user_type_id' => 'administrator',
                'scope_id' => 'administrator',
            ],
            [
                'user_type_id' => 'administrator',
                'scope_id' => 'user',
            ],
            [
                'user_type_id' => 'administrator',
                'scope_id' => 'authenticated',
            ],
        ];

        $table = $this->table('user_type_scopes');
        $table->insert($data)
            ->save();

        $data = [
            ['scope_id' => 'client'],
            ['scope_id' => 'service'],
            ['scope_id' => 'admin'],
            ['scope_id' => 'user'],
            ['scope_id' => 'support'],
            ['scope_id' => 'administrator'],
            ['scope_id' => 'authenticated'],
        ];

        $table = $this->table('auth_scopes');
        $table->insert($data)
            ->save();
    }
}

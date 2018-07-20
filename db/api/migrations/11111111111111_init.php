<?php


use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('clients', ['id' => false, 'primary_key' => ['client_id']]);
        $table->addColumn('client_id', 'string', ['limit' => 40])
            ->addColumn('client_type_id', 'string', ['limit' => 20])
            ->addColumn('title', 'string', ['limit' => 80])
            ->addColumn('secret', 'string', ['limit' => 80])
            ->addColumn('redirect_url', 'string', ['limit' => 300, 'null' => true])
            ->addColumn('status', 'enum', ['values' => ['active', 'disabled']])
            ->addColumn('timestamp_created', 'biginteger')
            ->addColumn('timestamp_updated', 'biginteger')
            ->create();

        $table = $this->table('client_types', ['id' => false, 'primary_key' => ['client_type_id']]);
        $table->addColumn('client_type_id', 'string', ['limit' => 20])
            ->addColumn('title', 'string', ['limit' => 80])
            ->create();

        $table = $this->table('client_type_scopes', ['id' => false, 'primary_key' => ['client_type_id', 'scope_id']]);
        $table->addColumn('client_type_id', 'string', ['limit' => 20])
            ->addColumn('scope_id', 'string', ['limit' => 30])
            ->addIndex(['scope_id'])
            ->create();

        /* user scopes that can be used with specific client */
        $table = $this->table('client_type_user_scopes', ['id' => false, 'primary_key' => ['client_type_id', 'scope_id']]);
        $table->addColumn('client_type_id', 'string', ['limit' => 20])
            ->addColumn('scope_id', 'string', ['limit' => 30])
            ->addIndex(['scope_id'])
            ->create();

        $table = $this->table('authenticators', ['id' => false, 'primary_key' => ['authenticator_id']]);
        $table->addColumn('authenticator_id', 'string', ['limit' => 20])
            ->addColumn('title', 'string', ['limit' => 30])
            ->create();

        $table = $this->table('users', ['id' => false, 'primary_key' => ['user_id']]);
        $table->addColumn('user_id', 'string', ['limit' => 20])
            ->addColumn('user_type_id', 'string', ['limit' => 20])
            ->addColumn('email', 'string', ['limit' => 80])
            ->addColumn('password', 'string', ['limit' => 80])
            ->addColumn('status', 'enum', ['values' => ['active', 'disabled']])
            ->addColumn('authenticator_id', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('timestamp_created', 'biginteger')
            ->addColumn('timestamp_updated', 'biginteger')
            ->addIndex(['email'])
            ->create();

        $table = $this->table('user_authenticators', ['id' => false, 'primary_key' => ['user_id', 'authenticator_id']]);
        $table->addColumn('user_id', 'string', ['limit' => 20])
            ->addColumn('authenticator_id', 'string', ['limit' => 20])
            ->addColumn('setup', 'enum', ['values' => ['done'], 'null' => true])
            ->addColumn('params', 'string', ['limit' => 500, 'null' => true])
            ->create();

        $table = $this->table('user_types', ['id' => false, 'primary_key' => ['user_type_id']]);
        $table->addColumn('user_type_id', 'string', ['limit' => 20])
            ->addColumn('title', 'string', ['limit' => 80])
            ->create();

        $table = $this->table('user_type_scopes', ['id' => false, 'primary_key' => ['user_type_id', 'scope_id']]);
        $table->addColumn('user_type_id', 'string', ['limit' => 20])
            ->addColumn('scope_id', 'string', ['limit' => 30])
            ->addIndex(['scope_id'])
            ->create();

        $table = $this->table('auth_scopes', ['id' => false, 'primary_key' => ['scope_id']]);
        $table->addColumn('scope_id', 'string', ['limit' => 30])
            ->create();

        $table = $this->table('auth_access_tokens', ['id' => false, 'primary_key' => ['access_token_id']]);
        $table->addColumn('access_token_id', 'string', ['limit' => 100])
            ->addColumn('client_id', 'string', ['limit' => 40])
            ->addColumn('user_id', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('scopes', 'string', ['limit' => 5000, 'null' => true])
            ->addColumn('timestamp_expire', 'biginteger')
            ->addColumn('timestamp_created', 'biginteger')
            ->addColumn('timestamp_updated', 'biginteger')
            ->addIndex(['client_id', 'user_id'], ['unique' => true])
            ->create();

        $table = $this->table('auth_codes', ['id' => false, 'primary_key' => ['code_id']]);
        $table->addColumn('code_id', 'string', ['limit' => 100])
            ->addColumn('client_id', 'string', ['limit' => 40])
            ->addColumn('user_id', 'string', ['limit' => 20])
            ->addColumn('scopes', 'string', ['limit' => 5000, 'null' => true])
            ->addColumn('redirect_url', 'string', ['limit' => 300, 'null' => true])
            ->addColumn('timestamp_expire', 'biginteger')
            ->addColumn('timestamp_created', 'biginteger')
            ->addColumn('timestamp_updated', 'biginteger')
            ->addIndex(['client_id', 'user_id'], ['unique' => true])
            ->create();

        $table = $this->table('auth_refresh_tokens', ['id' => false, 'primary_key' => ['refresh_token_id']]);
        $table->addColumn('refresh_token_id', 'string', ['limit' => 100])
            ->addColumn('access_token_id', 'string', ['limit' => 100])
            ->addColumn('timestamp_expire', 'biginteger')
            ->addColumn('timestamp_created', 'biginteger')
            ->addColumn('timestamp_updated', 'biginteger')
            ->addIndex(['access_token_id'])
            ->create();


    }

}

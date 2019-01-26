<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateUsersTable extends AbstractMigration
{
    protected $table = 'users';

    public function up()
    {
        $table = $this->table($this->table, [
                'engine' => 'MyISAM', 'collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'
            ]
        );
        $table->addColumn('email',                  'string',   ['limit' => 60, 'null' => false])
              ->addColumn('password',               'string',   ['limit' => 60, 'null' => false])
              ->addColumn('password_reset_token',   'string',   ['limit' => 32, 'null' => true])
              ->addColumn('activation_token',       'string',   ['limit' => 32, 'null' => true])
              ->addColumn('first_name',             'string',   ['limit' => 40, 'null' => false])
              ->addColumn('last_name',              'string',   ['limit' => 40, 'null' => true])
              ->addColumn('company_name',           'string',   ['limit' => 40, 'null' => true])
              ->addColumn('contact_number',         'string',   ['limit' => 16, 'null' => true])
              ->addColumn('dtm_created',            'timestamp',      ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
              ->addColumn('status',                 'integer',        ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0])

              // INDEXES
              ->addIndex(['email'], ['unique' => true])

              ->save();
    }

    public function down()
    {
      $this->table($this->table)->drop()->save();
    }
}

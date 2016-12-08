<?php

use Phinx\Migration\AbstractMigration;

class SetupDatabaseMigration extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('post');
        $table->addColumn('title', 'string', ['null' => true])
            ->addColumn('imageUrl', 'string', ['limit' => 250])
            ->addColumn('created', 'datetime')
            ->addIndex(['imageUrl'], ['unique' => true])
            ->create();

        $table->changeColumn('id', 'string', ['limit' => 250]);

        $table = $this->table('user');
        $table->addColumn('username', 'string', ['limit' => 255])
            ->addColumn('password', 'string', ['limit' => 250])
            ->addIndex(['username'], ['unique' => true])
            ->create();
    }
}

<?php

use Phinx\Migration\AbstractMigration;

class SetupDatabaseMigration extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('post');
        $table->addColumn('title', 'string', ['null' => true])
            ->addColumn('imageUrl', 'string', ['limit' => 250])
            ->addIndex(['imageUrl'], ['unique' => true])
            ->create();

        $table = $this->table('user');
        $table->addColumn('username', 'string', ['limit' => 255])
            ->addColumn('password', 'string', ['limit' => 250])
            ->addIndex(['username'], ['unique' => true])
            ->create();
    }
}

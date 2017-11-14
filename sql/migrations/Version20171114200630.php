<?php

namespace EcdbMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171114200630 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        /**
         * member password
         */
        $this->addSql('ALTER TABLE members MODIFY passwd VARCHAR(100) NOT NULL');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        /**
         * member password
         */
        $this->addSql('ALTER TABLE members MODIFY passwd VARCHAR(32) NOT NULL');

    }
}

<?php

namespace EcdbMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates initial database if does not exists.
 */
class Version20171102194710 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        /**
         * cms_pages
         */
        $this->addSql(<<<SQL
            CREATE TABLE `cms_pages` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(50) NOT NULL,
              `content` text,
              `title` varchar(100) DEFAULT NULL,
              `visibility` int(11) NOT NULL DEFAULT '1',
              PRIMARY KEY (`id`),
              UNIQUE KEY `cms_pages_name_uindex` (`name`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
SQL
        );
        /**
         * menu
         */
        $this->addSql(<<<SQL
            CREATE TABLE `menu` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(50) NOT NULL DEFAULT '',
              `base_color` varchar(15) NOT NULL DEFAULT '',
              `sort_nr` int(11) NOT NULL DEFAULT '0',
              `icon` varchar(20) NOT NULL DEFAULT '',
              `type` int(11) NOT NULL,
              `link` varchar(50) NOT NULL DEFAULT '',
              `visibility` int(11) NOT NULL DEFAULT '0',
              `select_route_names` varchar(255) NOT NULL DEFAULT '',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
SQL
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        /**
         * cms_pages
         */
        $this->addSql('DROP TABLE `cms_pages`');
        /**
         * menu
         */
        $this->addSql('DROP TABLE `menu`');
    }
}

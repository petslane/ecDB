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

        $this->addSql(<<<SQL
INSERT INTO cms_pages (id, name, content, title, visibility) VALUES (1, 'donate', 'ecDB is completely free!

However, if you like ecDB you may use the button below to donate some money to the project!


<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="7ZT5UY5XMHA52">
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
</form>
', 'Donate', 1)
SQL
        );

        $this->addSql(<<<SQL
INSERT INTO cms_pages (id, name, content, title, visibility) VALUES (2, 'public-components', '<div class="message orange">
    When you add a component there is a button called "public". If you choose to set that to yes, it means that other people can see that you own that component.<br><br>

    The thought with that setting is, for example; You are building a project and missed to order one component, to skip expensive shipping costs, long shipping time etc. you just make a quick search on ecDB for that component and contact the owner. Hopefully he is kind enough to send you that component quickly for a small charge.
</div>
    
# This function is under development...', 'Public Components', 1)
SQL
        );

        $this->addSql(<<<SQL
INSERT INTO cms_pages (id, name, content, title, visibility) VALUES (3, 'about', '<div class="left">
    <div class="message blue">
        Check out the new <a href="/blog">ecDB blog.</a> Or follow <a href="https://twitter.com/#!/ecDBnet">@ecDBnet</a>
        at Twitter to get the latest updates!
    </div>
    <h1>What is ecDB?</h1>

    ecDB is basically a place where you, as an electronics hobbyist (or professional) can add your own components to
    your personal database to keep track of what components you own, where they are, how many you own and so on.

    <br><br>
    <a href="/ecdb/img/about/index.png"><img src="/ecdb/img/about/index_thumbnail.png"></a>
    <a href="/ecdb/img/about/add.png"><img src="/ecdb/img/about/add_thumbnail.png"></a><br><br>
    <h1>Who &amp; Why?</h1>

    ecDB is created by <a target="_blank" href="http://nilsf.se">Nils Fredriksson</a> and designed by <a target="_blank"
                                                                                                         href="http://www.buildlog.eu">Buildlog</a>.
    <br><br>

    Me, Nils, have always wanted to have a system like this to keep track of what component I own. Before I created this
    system I (I guess you too...) had to dig through boxes filled with components to maybe find that component I needed.
    This is an unnecessary task to do, it not only takes time, and it also can be really frustrating not to find that
    component you are looking for. So I ended up creating this website where I easily can keep track of my components!

    <br><br>
    <h1>What does it cost?</h1>

    ecDB is completely free!<br>
    But if you like ecDB you can use this button to donate us some money.
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="7ZT5UY5XMHA52">
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit"
               alt="PayPal - The safer, easier way to pay online!">
    </form>

    <br>
    <h1>Is ecDB really done?</h1>
    No! ecDB is still under development. Here are some of the upcoming features:<br><br>

    - Public components - a place where you easily can trade components.<br>
    - View to physically print the personal database. Old-school typewritten text and nice colums!<br>
    - Datasheet and picture uploading.<br>
    - Advanced component search with parameters.<br>
    - Log for each component. See when the component last was used/edited/bought etc.<br>
    - Barcode implementation for easy storage management.<br>
    - Import and export of personal database to text/spreadsheet.<br>
    - Quick edit function of component data directly from the database view.<br>
    - Add personal categories and fields.<br>
    - Borrow component data from other components in the database to easily add components.
</div>
<div class="right"></div>
', '', 1)
SQL
        );

        $this->addSql(<<<SQL
INSERT INTO cms_pages (id, name, content, title, visibility) VALUES (4, 'contact', 'If you have any suggestions, questions or what not. Contact through the [ecDB GitHub Project](http://github.com/ElectricMan/ecDB)', 'Contact us', 1)
SQL
        );

        $this->addSql(<<<SQL
INSERT INTO cms_pages (id, name, content, title, visibility) VALUES (5, 'terms', '## 1. Terms

By accessing this web site, you are agreeing to be bound by these web site Terms and Conditions of Use, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this web site are protected by applicable copyright and trademark law.

<br />
## 2. Membership

As a condition to using the services you are required to register with ecDB. By registering with ecDB you certify that you always provide valid, and updated information, you are an individual (i.e., not a corporate entity) and that you have the legal rights to enter such an agreement. The ID and password (from now referred to as "login-data") is the sole responsibility. It is required that you, as a registered ecDB user maintain the safety of your own login-data.

<br />
ecDB maintains the right to terminate your membership at any time, with or without motivation or warning. All members are responsible for the consequences of use of this website. In cases of conflict with one or more non-members or members, will ecDB not be liable for any damages caused, in the current situation or future, resulting from the conflict.

<br />
As a registered ecDB user you warrant and agree to the fact that you will not contribute any content that (a) infringes, violates or otherwise interferes with any copyright or trademark of another party, (b) reveal any trade secret, unless you own the trade secret or has the owner''s permission to post it, (c) infringes any intellectual property right of another or the privacy or publicity rights of another, (d) is libelous, defamatory, abusive, threatening, harassing, hateful, offensive or otherwise violates any law or right of any third party.

<br />
## 3. Disclaimer

ecDB reserves all rights and disclaims all liability. ecDB makes no guarantee of reliability, safety or operation of this site.

As a registered user, you have full responsibility, without contradiction, for the information you publish and make widely available here.

<br />
## 4. Ownership
    
It is strictly forbidden to copy, distribute, or modify any material from ecDB. You may print material for private use. For all other use requires permission from ecDB.

<br />
## 5. Site Terms of Use Modifications

ecDB may revise these terms of use at any time without notice. By using ecDB you are agreeing to be bound by the then current version of these Terms and Conditions of Use.

<br />
## Privacy Policy

ecDB handles your personal information in accordance with the European data protection laws.

<br />
Third parties can get access to all the information you intended to make public through your settings. Your email address or other personal data is NEVER shared by us to third parties.
', 'Terms and Conditions & Privacy Policy', 1)
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

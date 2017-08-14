#!/usr/bin/env php
<?php
/**
 * Migrations CLI Application:
 *
 * Usage:
 * $ php console.php migrations:status
 * $ php console.php migrations:migrate
 */
require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

require_once __DIR__ . '/config/config.php';

$db = DriverManager::getConnection(array(
    'dbname' => $config['db']['db'],
    'user' => $config['db']['username'],
    'password' => $config['db']['password'],
    'host' => $config['db']['host'],
    'driver' => 'pdo_mysql',
    'charset' => 'utf8',
    'driverOptions' => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    )
));

$helperSet = new HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($db),
    'dialog' => new \Symfony\Component\Console\Helper\QuestionHelper(),
));

$console = new Application;
$console->setHelperSet($helperSet);
$console->addCommands(array(
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand,
));

$console->run();
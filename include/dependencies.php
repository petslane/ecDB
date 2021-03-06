<?php


$container = $app->getContainer();

$container['LoginController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\LoginController($app);
};
$container['RegisterController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\RegisterController($app);
};
$container['ProjectController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\ProjectController($app);
};
$container['MemberController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\MemberController($app);
};
$container['ShopController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\ShopController($app);
};
$container['ComponentController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\ComponentController($app);
};
$container['AjaxController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\AjaxController($app);
};
$container['CmsController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\CmsController($app);
};
$container['AdminController'] = function ($container) use ($app) {
    return new \Ecdb\Controllers\AdminController($app);
};
$container['view'] = function ($container) use ($ECDB_VERSION, $config) {
    $smarty = new Smarty();
    $smarty->setTemplateDir(__DIR__ . '/../views');
    $smarty->setCompileDir($config['smarty_compile_dir']);
    $smarty->assign('ECDB_VERSION', $ECDB_VERSION);
    $smarty->error_reporting = E_ALL & ~E_NOTICE;

    /** @var \Doctrine\DBAL\Connection $db */
    $db = $container['db'];

    $data1 = $db->fetchAssoc('SELECT COUNT(member_id) AS count FROM members');
    $data2 = $db->fetchAssoc('SELECT COUNT(id) AS count FROM data');
    $data3 = $db->fetchAssoc('SELECT COUNT(project_id) AS count FROM projects');

    $STATS = array(
        'members' => $data1['count'],
        'components' => $data2['count'],
        'projects' => $data3['count'],
    );
    $smarty->assign('STATS', $STATS);

    $sp = new \Ecdb\Smarty\SmartyPlugins($container);
    $smarty->registerPlugin('function', 'pathFor', array($sp, 'pathFor'), false);
    $smarty->registerPlugin('function', 'mdIcon', array($sp, 'mdIcon'), false);
    $smarty->registerPlugin('modifier', 'menuColorBackground', array($sp, 'menuColorBackground'), false);

    return $smarty;
};
$container['validatePassword'] = function ($container) use ($app) {
    return function ($password, $passwordHash) {
        if (strlen($passwordHash) == 32) {
            return md5($password) == $passwordHash;
        }

        return password_verify($password, $passwordHash);
    };
};

$container['db'] = function ($container) use ($config) {
    $c = new \Doctrine\DBAL\Configuration();

    $connectionParams = array(
        'dbname' => $config['db']['db'],
        'user' => $config['db']['username'],
        'password' => $config['db']['password'],
        'host' => $config['db']['host'],
        'driver' => 'pdo_mysql',
    );

    return \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $c);
};

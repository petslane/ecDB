<?php

$app->get('/', 'ComponentController:listing')->setName('index');
$app->get('/category', 'ComponentController:listing')->setName('index');
$app->get('/component/{id:[0-9]+}', 'ComponentController:view')->setName('component');
$app->get('/component/{id:[0-9]+}/edit', 'ComponentController:edit')->setName('component_edit');
$app->post('/component/{id:[0-9]+}/edit', 'ComponentController:save')->setName('component_edit');
$app->post('/component/{id:[0-9]+}/delete', 'ComponentController:delete')->setName('component_delete');
$app->get('/component/add', 'ComponentController:add')->setName('component_add');
$app->get('/component/add/{id:[0-9]+}', 'ComponentController:add')->setName('component_add');
$app->post('/component/add', 'ComponentController:save')->setName('component_add');
$app->post('/component/add/{id:[0-9]+}', 'ComponentController:save')->setName('component_add');
$app->get('/components/search', 'ComponentController:search')->setName('search');
$app->post('/ajax/change_component_count_field', 'AjaxController:component_count')->setName('ajax_component_count');
$app->get('/ajax/autocomplete', 'AjaxController:autocomplete')->setName('ajax_autocomplete');
$app->get('/login', 'LoginController:index')->setName('login');
$app->post('/auth', 'LoginController:auth')->setName('auth');
$app->get('/logout', 'LoginController:logout')->setName('logout');
$app->get('/register', 'RegisterController:index')->setName('register');
$app->post('/register', 'RegisterController:register')->setName('register');
$app->get('/proj_list', 'ProjectController:projects')->setName('projects');
$app->post('/project_add', 'ProjectController:add')->setName('project_add');
$app->get('/project/{id}/edit', 'ProjectController:edit')->setName('project_edit');
$app->post('/project/{id}/edit', 'ProjectController:edit')->setName('project_edit');
$app->get('/project/{id}', 'ProjectController:view')->setName('project');
$app->get('/my', 'MemberController:edit')->setName('member_edit');
$app->post('/my', 'MemberController:edit')->setName('member_edit');
$app->get('/shoplist', 'ShopController:index')->setName('shoplist');
$app->get('/admin', 'AdminController:index')->setName('admin');
$app->get('/admin/cms', 'CmsController:admin')->setName('admin_cms');
$app->get('/admin/cms/new', 'CmsController:create')->setName('admin_cms_new');
$app->post('/admin/cms/new', 'CmsController:save_new')->setName('admin_cms_new_save');
$app->get('/admin/cms/{id}', 'CmsController:view')->setName('admin_cms_view');
$app->get('/admin/cms/{id}/edit', 'CmsController:edit')->setName('admin_cms_edit');
$app->post('/admin/cms/{id}/edit', 'CmsController:save')->setName('admin_cms_edit');
$app->post('/admin/cms/{id}/delete', 'CmsController:delete')->setName('admin_cms_delete');
$app->post('/admin/cms/md-preview', 'CmsController:ajax_preview')->setName('admin_cms_md_preview');
$app->get('/admin/menu', 'AdminController:menu')->setName('admin_menu');
$app->get('/admin/menu/{id}/edit', 'AdminController:menu_edit')->setName('admin_menu_edit');
$app->post('/admin/menu/{id}/edit', 'AdminController:menu_save')->setName('admin_menu_edit_save');
$app->get('/admin/menu/new', 'AdminController:menu_new')->setName('admin_menu_new');
$app->post('/admin/menu/new', 'AdminController:menu_save')->setName('admin_menu_new_save');
$app->post('/admin/menu/{id}/delete', 'AdminController:menu_delete')->setName('admin_menu_delete');
$app->post('/ajax/admin/menu/sort', 'AdminController:ajax_menu_sort')->setName('ajax_admin_sort');
$app->get('/{page}', 'CmsController:index')->setName('cms');

// redirect to php file
$app->any('/{filename}.php', function ($request, \Slim\Http\Response $response, $args) {
    $filename = realpath($args['filename'] . '.php');
    if (!$filename) {
        return $response->withStatus(404);
    }
    $base = dirname($filename);
    if (dirname(__DIR__) != $base) {
        return $response->withStatus(404);
    }

    require_once $filename;
});

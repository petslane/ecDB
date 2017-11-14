<?php

$app->add(function ($request, $response, $next) {
    // set base url variable in view
    $base_url = $request->getUri()->getBaseUrl();
    $this->view->assign('base_url', $base_url);

    $types = array(
        'ERRMSG_ARR' => 'errors',
        'messages' => 'messages',
        'info' => 'info',
    );

    $data = array();
    foreach ($types as $session_var=>$smarty_var) {
        if (!empty($_SESSION[$session_var])) {
            $this->view->assign($smarty_var, $_SESSION[$session_var]);
            $data[$session_var] = $_SESSION[$session_var];
            unset($_SESSION[$session_var]);
        }
    }

    $response = $next($request, $response);

    // not normal page, put all flash messages back to session
    if (!$response->isOk()) {
        foreach ($types as $session_var=>$smarty_var) {
            if (!empty($data[$session_var])) {
                $new_data = !empty($_SESSION[$session_var]) ? $_SESSION[$session_var] : array();
                $smarty_data = empty($this->view->tpl_vars[$smarty_var]->value) ? array() : $this->view->tpl_vars[$smarty_var]->value;
                $_SESSION[$session_var] = array_merge($new_data, $smarty_data, $data[$session_var]);
            }
        }
    }

    return $response;
});

$app->add(function ($request, $response, $next) {
    $route = $request->getAttribute('route');

    // index for non existent route
    if (empty($route)) {
        $path = $this->get('router')->pathFor('index');
        return $response->withRedirect($path);
    }

    $name = $route->getName();
    # $groups = $route->getGroups();
    # $methods = $route->getMethods();
    # $arguments = $route->getArguments();

    $public_route_names = array(
        'login',
        'register',
        'auth',
    );

    if ($name === 'cms') {
        // CMS page, get all public CMS pages from DB
        $name .= ':' . $route->getArgument('page', '');

        $public_cms_names = $this->get('db')->fetchAll('
        SELECT CONCAT("cms:", c.name) as cms_name
          FROM cms_pages c
         WHERE c.visibility = 1', array());
        $public_cms_names = array_map(function ($row) {
            return $row['cms_name'];
        }, $public_cms_names);

        $public_route_names = array_merge($public_route_names, $public_cms_names);
    }

    if (empty($_SESSION['SESS_MEMBER_ID']) && !in_array($name, $public_route_names)) {
        $path = $this->get('router')->pathFor('login');
        return $response->withRedirect($path);
    }

    $admin_route_names = array(
        'admin',
        'admin_cms',
        'admin_cms_new',
        'admin_cms_new_save',
        'admin_cms_view',
        'admin_cms_edit',
        'admin_cms_delete',
        'admin_cms_md_preview',
        'admin_menu',
        'admin_menu_edit',
        'admin_menu_edit_save',
        'admin_menu_new',
        'admin_menu_new_save',
        'admin_menu_delete',
        'ajax_admin_sort',
    );

    if ((empty($_SESSION['SESS_MEMBER_ID']) || empty($_SESSION['SESS_IS_ADMIN'])) && in_array($name, $admin_route_names)) {
        $path = $this->get('router')->pathFor('login');
        return $response->withRedirect($path);
    }

    return $next($request, $response);
});

$app->add(function ($request, $response, $next) use ($config) {

    $this->view->assign('ga', $config['google_analytics']);

    return $next($request, $response);
});

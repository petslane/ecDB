<?php

namespace Ecdb\Controllers;

use Slim\Route;

class AdminController extends BaseController {

    public function index(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        return $this->render('admin.tpl');
    }

    /**
     * Admin menu list
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     */
    public function menu(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $sql = 'seleCT * FROM menu ORDER BY sort_nr ASC';
        $data = $this->db->fetchAll($sql);

        $this->view->assign('data', $data);

        return $this->render('admin-menu-list.tpl');
    }

    /**
     * Ajax method for ordering menu items
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return static
     */
    public function ajax_menu_sort(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $order = $req->getParam('order');
        $nr = 1;

        $db = $this->db;
        array_map(function ($menu_id) use (&$nr, $db) {
            $db->update('menu', array(
                'sort_nr' => $nr,
            ), array(
                'id' => $menu_id,
            ));
            $nr++;
        }, $order);

        $data = $db->fetchAll('SELECT id FROM menu ORDER BY sort_nr ASC');
        $data = array_map(function ($row) {
            return (int) $row['id'];
        }, $data);

        return $response->withJson(array(
            'order' => $data,
        ));
    }

    /**
     * Save menu item
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function menu_save(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $routeName = $req->getAttribute('route')->getName();
        if ($routeName == 'admin_menu_edit_save' && empty($args['id'])) {
            return $this->redirect($response, 'admin_menu');
        }
        $id = $routeName == 'admin_menu_edit_save' ? $args['id'] : null;

        $data = $req->getParam('menu');

        $link = '';
        if ($data['type'] == 1) {
            $link = $data['link_internal'];
        } else if ($data['type'] == 2) {
            $link = $data['link_cms'];
        } else if ($data['type'] == 3) {
            $link = $data['link_url'];
        }
        $icon = '';
        if ($data['icon_type'] == 'md') {
            $icon = str_replace(' ', '_', $data['icon_md']);
        } else if ($data['icon_type'] == 'old') {
            $icon = $data['icon_old'];
        }

        $saveData = array(
            'title' => $data['title'],
            'base_color' => !empty($data['use_base_color']) ? $data['base_color'] : '',
            'icon' =>  $icon,
            'type' => $data['type'],
            'link' => $link,
            'visibility' => $data['visibility'],
            'select_route_names' => $data['select_route_names'],
        );

        $errors = array();
        if ($error = $this->validateMenuTitle($saveData['title'])) {
            $errors[] = $error;
        }
        if ($error = $this->validateMenuBaseColor($saveData['base_color'])) {
            $errors[] = $error;
        }
        if ($error = $this->validateMenuLinkType($saveData['type'])) {
            $errors[] = $error;
        }
        if ($error = $this->validateMenuLink($saveData['link'], $saveData['type'])) {
            $errors[] = $error;
        }
        if ($error = $this->validateMenuVisibility($saveData['visibility'])) {
            $errors[] = $error;
        }

        if (!$errors) {
            if ($routeName == 'admin_menu_new_save') {
                $sortNr = $this->db->fetchColumn('SELECT MAX(sort_nr) FROM menu');
                $saveData['sort_nr'] = $sortNr + 1;
                $this->db->insert('menu', $saveData);
                $id = $this->db->lastInsertId();
            } else {
                $this->db->update('menu', $saveData, array(
                    'id' => $id,
                ));
            }

            return $this->redirect($response, 'admin_menu_edit', array(
                'id' => $id,
            ));
        }

        array_map(function ($error) {
            $_SESSION['ERRMSG_ARR'][] = $error;
        }, $errors);

        $this->view->assign('data', $data);

        $this->view->assign('icons', $this->getAllOldIconNames());
        $this->view->assign('routes', $this->getAllRoutesNames());
        $this->view->assign('cms_pages', $this->getCmsPages());

        $this->view->assign('newPage', $routeName == 'admin_menu_new_save');

        return $this->render('admin-menu-form.tpl');
    }

    /**
     * Delete menu item
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function menu_delete(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        if (empty($args['id'])) {
            return $this->redirect($response, 'admin_menu');
        }

        $this->db->delete('menu', array(
            'id' => $args['id'],
        ));

        $_SESSION['messages'][] = 'Menu deleted';

        return $this->redirect($response, 'admin_menu');
    }

    /**
     * Validate menu link
     *
     * @param $value
     * @param $type
     * @return null|string
     */
    protected function validateMenuLink($value, $type) {
        $internalPages = $this->getAllRoutesNames();
        if ($type == 1 && !in_array($value, $internalPages)) { // internal page
            return 'Invalid internal page link';
        }
        $cmsPages = $this->getCmsPages();
        $cmsPages = array_map(function ($row) {
            return $row['id'];
        }, $cmsPages);
        if ($type == 2 && !in_array($value, $cmsPages)) { // cms page
            return 'Invalid CMS page link';
        }
        if ($type == 3 && !preg_match('#^https?://#', $value)) { // cms page
            return 'Invalid external link';
        }

        return null;
    }

    /**
     * Validate menu link type
     *
     * @param $value
     * @return null|string
     */
    protected function validateMenuLinkType($value) {
        if (!in_array($value, array(1, 2, 3))) {
            return 'Menu link type allowed values are 1, 2 and 3';
        }

        return null;
    }

    /**
     * Validate menu visibility
     *
     * @param $value
     * @return null|string
     */
    protected function validateMenuVisibility($value) {
        if (!in_array($value, array(0, 1, 2, 3))) {
            return 'Menu visibility allowed values are 0, 1, 2 and 3';
        }

        return null;
    }

    /**
     * Validate menu title
     *
     * @param $value
     * @return null|string
     */
    protected function validateMenuTitle($value) {
        $length = strlen($value);
        if ($length > 50) {
            return 'Menu title max length is 50 characters';
        }

        return null;
    }

    /**
     * Validate menu color
     *
     * @param $value
     * @return null|string
     */
    protected function validateMenuBaseColor($value) {
        $value = strtolower($value);
        if (!$value) {
            return null;
        }
        if (!ctype_xdigit(substr($value, 1)) || !substr($value, 0, 1) == '#' || !strlen($value) == 7) {
            return 'Base color format: #RRGGBB';
        }

        return null;
    }

    /**
     * Add new menu item
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     */
    public function menu_new(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {

        $this->view->assign('data', array());

        $this->view->assign('icons', $this->getAllOldIconNames());
        $this->view->assign('routes', $this->getAllRoutesNames());
        $this->view->assign('cms_pages', $this->getCmsPages());
        $this->view->assign('newPage', true);

        return $this->render('admin-menu-form.tpl');
    }

    /**
     * Edit menu item
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function menu_edit(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {

        if (empty($args['id'])) {
            return $this->redirect($response, 'admin_menu');
        }

        $sql = 'select * from menu where id = ?';
        $data = $this->db->fetchAssoc($sql, array(
            $args['id'],
        ));
        $this->view->assign('data', $data);

        $this->view->assign('icons', $this->getAllOldIconNames());
        $this->view->assign('routes', $this->getAllRoutesNames());
        $this->view->assign('cms_pages', $this->getCmsPages());

        return $this->render('admin-menu-form.tpl');
    }

    /**
     * Get all available old icon names
     *
     * @return array
     */
    protected function getAllOldIconNames() {
        $icons = array(
            'old-checkboxChecked',
            'old-checkboxUnchecked',
            'old-checkmark',
            'old-document',
            'old-key',
            'old-pencil',
            'old-picture',
            'old-print',
            'old-roundMinus',
            'old-roundPlus',
            'old-save',
            'old-spechBubble',
            'old-spechBubbleSq',
            'old-trash',
            'old-sqPlus',
            'old-docLinesStright',
            'old-shopCart',
            'old-cube',
            'old-inbox',
            'old-user',
            'old-curDollar',
            'old-shre',
        );

        return $icons;
    }

    /**
     * Get all route names
     *
     * @return array
     */
    protected function getAllRoutesNames() {
        $routes = $this->app->getContainer()->router->getRoutes();
        $routes = array_filter($routes, function ($route) {
            /** @var Route $route */
            if (!$route->getName()) {
                return false;
            }

            return true;
        });
        $routes = array_map(function ($route) {
            return $route->getName();
        }, $routes);
        $routes = array_unique($routes);
        sort($routes);

        return $routes;
    }

    /**
     * Get CMS pages for autocomplete
     *
     * @return array
     */
    protected function getCmsPages() {
        $sql = 'SELECT * FROM cms_pages';
        $cms_pages = $this->db->fetchAll($sql);

        $cms_pages = array_map(function ($row) {
            return array(
                'id' => $row['id'],
                'name' => "\"{$row['title']}\" (/{$row['name']})",
            );
        }, $cms_pages);

        return $cms_pages;
    }

}


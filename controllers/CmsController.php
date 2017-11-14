<?php

namespace Ecdb\Controllers;

class CmsController extends BaseController {

    /**
     * List CMS pages
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     */
    public function admin(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $sql = 'SELECT * FROM cms_pages ORDER BY id ASC';
        $data = $this->db->fetchAll($sql);

        $this->view->assign('data', $data);

        return $this->view->display('admin-cms-list.tpl');
    }

    /**
     * View CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function view(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        if (empty($args['id'])) {
            return $this->redirect($response, 'admin_cms');
        }

        $sql = "select * from cms_pages where id = ?";
        $data = $this->db->fetchAssoc($sql, array($args['id']));
        if (!$data) {
            return $this->redirect($response, 'admin_cms');
        }

        $this->view->assign('data', $data);

        $Parsedown = new \Parsedown();
        $content = $Parsedown->text($data['content']);
        $this->view->assign('content', $content);

        return $this->view->display('admin-cms-view.tpl');
    }

    /**
     * Get CMS page
     *
     * @param $id
     * @return array
     * @throws \Exception
     */
    protected function getCmsPage($id) {
        $sql = "select * from cms_pages where id = ?";
        $data = $this->db->fetchAssoc($sql, array($id));

        if (!$id) {
            throw new \Exception('CMS Page not found');
        }

        return $data;
    }

    /**
     * Save CMS page
     *
     * @param $name
     * @param $content
     * @param $title
     * @param null $edit_id
     * @return null|string
     * @throws \Exception
     */
    protected function savePage($name, $content, $title, $edit_id=null) {
        if (!preg_match('/^[a-zA-Z0-9\\-]*$/', $name)) {
            throw new \Exception('Page path allowed characters are: a-z, 0-9, -');
        }
        if (strlen($name) < 3) {
            throw new \Exception('Page path length can not me less then 3 characters long');
        }

        $sql = "select count(id) as c from cms_pages where name = ?";
        $params = array(
            $name,
        );
        if ($edit_id) {
            $sql .= " AND id != ?";
            $params[] = $edit_id;
        }
        $data = $this->db->fetchAssoc($sql, $params);
        if ($data['c']) {
            throw new \Exception('Page path is already used by another page');
        }

        if ($edit_id) {
            $this->db->update('cms_pages', array(
                'name' => $name,
                'content' => $content,
                'title' => $title,
            ), array(
                'id' => $edit_id,
            ));
            return $edit_id;
        }

        $this->db->insert('cms_pages', array(
            'name' => $name,
            'content' => $content,
            'title' => $title,
        ));
        return $this->db->lastInsertId();
    }

    /**
     * Delete CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function delete(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        if (empty($args['id'])) {
            return $this->redirect($response, 'admin_cms');
        }

        $this->db->delete('cms_pages', array(
            'id' => $args['id'],
        ));

        $_SESSION['messages'][] = 'Page deleted';

        return $this->redirect($response, 'admin_cms');
    }

    /**
     * Create new CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     */
    public function create(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $name = $fname = $req->getParam('name');
        $content = $fname = $req->getParam('content');
        $title = $fname = $req->getParam('title');

        $data['name'] = $name;
        $data['content'] = $content;
        $data['title'] = $title;

        $this->view->assign('data', $data);
        $this->view->assign('newPage', true);

        return $this->render('admin-cms-edit.tpl');
    }

    /**
     * Save new CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function save_new(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $name = $fname = $req->getParam('name');
        $content = $fname = $req->getParam('content');
        $title = $fname = $req->getParam('title');

        try {
            $id = $this->savePage($name, $content, $title);
            $_SESSION['messages'][] = 'Saved';
            return $this->redirect($response, 'admin_cms_edit', array('id' => $id));
        } catch (\Exception $e) {
            $_SESSION['ERRMSG_ARR'][] = $e->getMessage();
        }

        return $this->create($req, $response, array());
    }

    /**
     * Save existing CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function save(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        if (empty($args['id'])) {
            return $this->redirect($response, 'admin_cms');
        }

        try {
            $data = $this->getCmsPage($args['id']);
        } catch (\Exception $e) {
            return $this->redirect($response, 'admin_cms');
        }

        $name = $fname = $req->getParam('name');
        $content = $fname = $req->getParam('content');
        $title = $fname = $req->getParam('title');

        try {
            $this->savePage($name, $content, $title, $args['id']);
            $_SESSION['messages'][] = 'Saved';
            return $this->redirect($response, 'admin_cms_edit', array('id' => $args['id']));
        } catch (\Exception $e) {
            $_SESSION['ERRMSG_ARR'][] = $e->getMessage();
        }

        $data['name'] = $name;
        $data['content'] = $content;
        $data['title'] = $title;

        $this->view->assign('data', $data);

        $Parsedown = new \Parsedown();
        $content = $Parsedown->text($data['content']);
        $this->view->assign('content', $content);

        return $this->render('admin-cms-edit.tpl');
    }

    /**
     * Edit CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return mixed
     */
    public function edit(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        if (empty($args['id'])) {
            return $this->redirect($response, 'admin_cms');
        }

        try {
            $data = $this->getCmsPage($args['id']);
        } catch (\Exception $e) {
            return $this->redirect($response, 'admin_cms');
        }

        $this->view->assign('data', $data);

        $Parsedown = new \Parsedown();
        $content = $Parsedown->text($data['content']);
        $this->view->assign('content', $content);

        return $this->view->display('admin-cms-edit.tpl');
    }

    /**
     * Preview CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return static
     */
    public function ajax_preview(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $md = $req->getParam('md');

        $Parsedown = new \Parsedown();
        $content = $Parsedown->text($md);

        return $response->withJson(array(
            'html' => '<div>' . $content . '</div>',
        ));
    }

    /**
     * View CMS page
     *
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $response
     * @param $args
     * @return static
     */
    public function index(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {
        $page = !empty($args['page']) ? $args['page'] : '';

        $sql = "select * from cms_pages where name = ?";
        $data = $this->db->fetchAssoc($sql, array(
            $page,
        ));

        if (!$data) {
            return $response->withStatus(404);
        }

        $this->view->assign('menu_route_cms', $data['id']);

        $Parsedown = new \Parsedown();

        $this->view->assign('data', $data);

        $content = $Parsedown->text($data['content']);
        $this->view->assign('content', $content);

        return $this->view->display('cms-page-view.tpl');
    }

}


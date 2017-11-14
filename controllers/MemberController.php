<?php

namespace Ecdb\Controllers;

class MemberController extends BaseController {

    private function inPasswordValid($member_id, $password) {
        $passwordHash = $this->db->fetchColumn('SELECT passwd FROM members WHERE member_id = ?', array(
            $member_id,
        ));

        $validatePassword = $this->app->getContainer()->get('validatePassword');
        return $validatePassword($password, $passwordHash);
    }

    public function edit(\Slim\Http\Request $req, \Slim\Http\Response $response, $args) {


        if($req->isPost()) {
            $owner = $_SESSION['SESS_MEMBER_ID'];
            $firstname = $req->getParam('firstname');
            $lastname = $req->getParam('lastname');
            $mail = $req->getParam('mail');
            $measurement = (int) $req->getParam('measurement');
            $currency = $req->getParam('currency');
            $oldpass = $req->getParam('oldpass');
            $newpass = $req->getParam('newpass');

            if (!in_array($currency, array('SEK', 'USD', 'EUR', 'GBP'))) {
                $currency = 'SEK';
            }

            if (!$firstname) {
                $_SESSION['ERRMSG_ARR'][] = 'First name missing';
            } else if (strlen($firstname) < 2) {
                $_SESSION['ERRMSG_ARR'][] = 'Minimum of 2 chars in first name.';
            }

            if (!$lastname) {
                $_SESSION['ERRMSG_ARR'][] = 'Last name missing';
            } else if (strlen($lastname) < 2) {
                $_SESSION['ERRMSG_ARR'][] = 'Minimum of 2 chars in last name.';
            }

            if (!$mail) {
                $_SESSION['ERRMSG_ARR'][] = 'Mail missing';
            } else if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['ERRMSG_ARR'][] = 'Invalid e-mail address';
            }

            if (!$oldpass && $newpass) {
                $_SESSION['ERRMSG_ARR'][] = 'For setting new password, please provide current password';
            } else if ($oldpass && !$newpass && strlen($newpass) < 5) {
                $_SESSION['ERRMSG_ARR'][] = 'Minimum of 5 chars in password';
            } else if ($oldpass && !$this->inPasswordValid($owner, $oldpass)) {
                $_SESSION['ERRMSG_ARR'][] = 'Provided current password is incorrect';
            }

            if (empty($_SESSION['ERRMSG_ARR'])) {
                $data = array(
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'mail' => $mail,
                    'measurement' => $measurement,
                    'currency' => $currency,
                );
                if ($oldpass && $newpass) {
                    $data['passwd'] = password_hash($newpass, PASSWORD_BCRYPT);
                }
                $login = $this->db->fetchColumn('SELECT login from members where member_id = ?', array(
                    $_SESSION['SESS_MEMBER_ID'],
                ));
                if ($login !== 'demo') {
                    $this->db->update('members', $data, array(
                        'member_id' => $owner,
                    ));
                } else {
                    $_SESSION['info'][] = 'This is Demo account, data is not actually saved';
                }
                $_SESSION['messages'][] = 'Settings updated';
                return $this->redirect($response, 'my');
            }
        }

        $data = $this->db->fetchAssoc('SELECT * FROM members WHERE member_id = ?', array(
            $_SESSION['SESS_MEMBER_ID']
        ));

        unset($data['passwd']);

        if ($req->isPost()) {
            $data['firstname'] = $firstname;
            $data['lastname'] = $lastname;
            $data['mail'] = $mail;
            $data['measurement'] = $measurement;
            $data['currency'] = $currency;
        }

        $this->view->assign('member', $data);

        $this->view->assign('selected_menu', 'my');

        return $this->render('member.tpl');
    }

}


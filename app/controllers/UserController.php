<?php

namespace App\Controllers;

use App\Models\Users;

class UserController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function loginAction()
    {
        $this->view->pageTitle = 'User Login';

        if ($this->auth->isUserLoggedIn()) {
            return $this->response->redirect("/");
        }

        if ($this->request->isPost() ) {

            // Receiving the variables sent by POST
            $username = $this->filter->sanitize($this->request->getPost('username'), "trim");
            $password = $this->filter->sanitize($this->request->getPost('password'), "trim");

            // find user in the database
            $user = Users::findFirst(array(
                "username = :username: AND password = :password: AND active = 'Y'",
                "bind" => array(
                    'username' => $username,
                    'password' => hash('sha256', $password),
                )
            ));

            if (!empty($user)) {
                $this->auth->userLogin($user);
                return $this->response->redirect("/");
            }

            //$this->getFlashSession('error', 'Wrong email/password.', false);
        }
    }

    public function logoutAction()
    {
        $this->auth->userLogout();
        return $this->response->redirect("/");
    }
}

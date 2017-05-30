<?php

namespace App\Controllers;

use App\Models\Users;

class UserController extends ControllerBase
{
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

    public function changePasswordAction()
    {
       $this->view->pageTitle = 'Change Password';

        if ($this->request->isPost()) {
            $oldPassword  = $this->request->getPost('oldpass');
            $newPassword  = $this->request->getPost('newpass');
            $retypePasswd = $this->request->getPost('newpass2');

            if ($newPassword != $retypePasswd) {
                $this->flash->error("Two new passwords must be same, please try again.");
                return; // retry
            }

            $auth = $this->auth->getUser();
            $user = Users::findFirst($auth['id']);

            if ($user && $user->password == hash('sha256', $oldPassword)) {
                try {
                    $user->password = hash('sha256', $newPassword);
                    $user->save();
                    $this->flash->success("Your password changed successfully.");
                } catch (\Exception $e) {
                    return; // retry
                }
                return $this->response->redirect("/");
            } else {
                $this->flash->error("Old password is incorrect, please try again.");
            }
        }
    }
}

<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

/**
 * ControllerBase
 * This is the base controller for all controllers in the application
 */
class ControllerBase extends Controller
{
    public function onConstruct()
    {
        $this->assets->addCss("/assets/css/style.css?v=".filemtime(BASE_DIR.'/public/assets/css/style.css'));
        $this->assets->addJs("/assets/js/script.js?v=".filemtime(BASE_DIR.'/public/assets/js/script.js'));
    }

    /**
     * Execute before the router so we can determine if this is a private controller, 
     * and must be authenticated, or a public controller that is open to all.
     *
     * @param Dispatcher $dispatcher
     * @return boolean
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $this->view->userLoggedIn = false;

        // Get the current identity
        $user = $this->auth->getUser();
        if (is_array($user)) {
            $this->view->userLoggedIn = true;
        }

        $controllerName = $dispatcher->getControllerName();

        // Only check permissions on private controllers
        if ($this->acl->isPrivate($controllerName)) {
            // If there is no identity available the user is redirected to user/login
            if (!is_array($user)) {
                $dispatcher->forward(array(
                    'controller' => 'user',
                    'action' => 'login'
                ));
                return false;
            }

            // Check if the user have permission to the current option
            $actionName = $dispatcher->getActionName();
            if (!$this->acl->isAllowed($user['role'], $controllerName, $actionName)) {
                $this->flash->error("You don't have access, please contact admin.");

                $dispatcher->forward(array(
                    'controller' => 'index',
                    'action' => 'index'
                ));

                return false;
            }
        }

        return true;
    }

    protected function runJob($name, $args = '')
    {
        // $name looks like 'job/Test'
        // exec('psexec -d c:/xampp/php/php ../job/Test.php');
        exec("psexec -d c:/xampp/php64/php ../$name.php $args");
    }

    protected function startDownload($filename)
    {
        if (file_exists($filename)) {
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header('Content-Type: application/txt');
            header('Content-Length: ' . filesize($filename));
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            readfile($filename);
            die();
        }
    }
}

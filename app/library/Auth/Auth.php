<?php

namespace App\Library\Auth;

use Phalcon\Mvc\User\Component;
use App\Models\Users;

class Auth extends Component
{
    const SESSKEY = 'auth';

    /**
     * Check if user logged in
     *
     * @return bool
     */
    public function isUserLoggedIn()
    {
        return is_array($this->session->get(self::SESSKEY));
    }

    /**
     * Login the user
     */
    public function userLogin($user)
    {
        $this->session->set(self::SESSKEY, $user->toArray());
    }

    /**
     * Logout the user
     */
    public function userLogout()
    {
        $this->session->destroy();
    }

    /**
     * Get user information
     */
    public function getUser()
    {
        return $this->session->get(self::SESSKEY);
    }
}

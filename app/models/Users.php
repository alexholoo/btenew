<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $username;
    public $email;
    public $password;
    public $role;
    public $active;
    public $createdon;
    public $updatedon;

    public function initialize()
    {
    }

    public function beforeSave()
    {
        // Convert the array into a string
        $this->role = implode(",", $this->role);
    }

    public function afterFetch()
    {
        // Convert the string to an array
        $this->role = array_map('trim', explode(',', $this->role));
    }

    public function afterSave()
    {
        // Convert the string to an array
        $this->role = array_map('trim', explode(',', $this->role));
    }
}

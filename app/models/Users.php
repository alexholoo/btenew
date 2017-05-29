<?php

namespace App\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

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

    public function validation()
    {
        $this->validate(new Uniqueness(
            array(
                "field"   => "username",
                "message" => "The username must be unique"
            )
        ));

        return $this->validationHasFailed() != true;
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

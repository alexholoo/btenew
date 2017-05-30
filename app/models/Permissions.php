<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class Permissions extends Model
{
    public $roleId;
    public $resource;

    public function initialize()
    {
        $this->setSource('role_permissions');
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'       => 'id',
            'role_id'  => 'roleId',
            'resource' => 'resource',
        );
    }
}

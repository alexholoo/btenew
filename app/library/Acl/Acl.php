<?php

namespace App\Library\Acl;

use Phalcon\Mvc\User\Component;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;

use App\Models\Roles;
use App\Models\Permissions;

/**
 * App\Library\Acl\Acl
 */
class Acl extends Component
{
    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * The file path of the ACL cache file from APP_DIR
     *
     * @var string
     */
    private $filePath = '/cache/acl/data.txt';

    /**
     * Define the resources that are considered "private".
     *
     * @var array
     */
    private $privateResources = array(
        'about' => [
            'test',
        ],
    );

    /**
     * Checks if a controller is private or not
     *
     * @param string $controllerName
     * @return boolean
     */
    public function isPrivate($controllerName)
    {
        $controllerName = strtolower($controllerName);
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Checks if the current profile is allowed to access a resource
     *
     * @param string $profile
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($roles, $controller, $action)
    {
        #if (in_array(Roles::ADMIN, $roles)) {
        #    return true;
        #}

        $acl = $this->getAcl();

        foreach ($roles as $role) {
            if ($acl->isAllowed($role, $controller, $action)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the ACL list
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }

        // Check if the ACL is already generated
        if (!file_exists(APP_DIR . $this->filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Get the ACL from the data file
        $data = file_get_contents(APP_DIR . $this->filePath);
        $this->acl = unserialize($data);

        return $this->acl;
    }

    /**
     * Rebuilds the access list into a file
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function rebuild()
    {
        $acl = new AclMemory();

        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        // Register roles
        $roles = Roles::find();

        foreach ($roles as $role) {
            $acl->addRole(new AclRole($role->id));
        }

        // Register resources
        foreach ($this->privateResources as $resource => $actions) {
            $acl->addResource(new AclResource($resource), $actions);
        }

        // Grant access to private area to role Users
        $permissions = Permissions::find();

        foreach ($permissions as $permission) {
            $resource = $permission->resource;

            list($controller, $action) = explode('/', $resource);

            // Grant permissions in "permissions" model
            $acl->allow($permission->roleId, $controller, $action);

            // Always grant these permissions
            //$acl->allow($profile->name, 'user', 'changePassword');
        }

        if (touch(APP_DIR . $this->filePath) && is_writable(APP_DIR . $this->filePath)) {
            file_put_contents(APP_DIR . $this->filePath, serialize($acl));
        } else {
            $this->flash->error(
                'The user does not have write permissions to create the ACL list at ' . APP_DIR . $this->filePath
            );
        }

        return $acl;
    }
}

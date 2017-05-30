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
     * Define the public resources
     *
     * @var array
     */
    private $publicResources = [
        'index/index' => '',
        'about/index' => '',
        'about/test' => '',
        'user/login' => '',
        'user/logout' => '',
        'search/order' => '',
        'search/sku' => '',
        'search/priceavail' => '',
        'search/shipment' => '',
        'search/address' => '',
        'invloc/search' => '',
        'search/invloc' => '',
        'amazon/reports' => '',
        'amazon/fbaitems' => '',
        'amazon/fbaitemdelete' => '',
        'job/test' => '',
        'job/orderimport' => '',
        'job/amazonupdate' => '',
        'job/importshippingeasy' => '',
        'query/shippingeasy' => '',
        'shipment/search' => '',
        'shipment/chitchat' => '',
        'query/shippingeasy' => '',
    ];

    /**
     * Checks if a url is private or not
     *
     * @param string $url
     * @return boolean
     */
    public function isPrivate($url)
    {
        return !isset($this->publicResources[$url]);
    }

    /**
     * Checks if the current profile is allowed to access a resource
     *
     * @param array  $roles
     * @param string $url
     * @return boolean
     */
    public function isAllowed($roles, $url)
    {
        // Admin can do anything
        if (in_array(Roles::ADMIN, $roles)) {
            return true;
        }

        $acl = $this->getAcl();

        foreach ($roles as $role) {
            if ($acl->isAllowed($role, $url, '')) {
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

        $changePassword = 'user/changepassword';
        $acl->addResource(new AclResource($changePassword), []);

        // Register roles
        $roles = Roles::find();

        foreach ($roles as $role) {
            $acl->addRole(new AclRole($role->id));
            $acl->allow($role->id, $changePassword, '*');
        }

        // Grant access to private area to roles
        $permissions = Permissions::find();

        foreach ($permissions as $permission) {
            // Register resources
            $resource = $permission->resource;
            $acl->addResource(new AclResource($resource), []);

            // Grant permissions in "permissions" model
            $acl->allow($permission->roleId, $resource, '*');
        }

        if (touch(APP_DIR . $this->filePath) && is_writable(APP_DIR . $this->filePath)) {
            file_put_contents(APP_DIR . $this->filePath, serialize($acl));
        } else {
            $this->flash->error('Failed to create the ACL list at ' . APP_DIR . $this->filePath);
        }

        return $acl;
    }
}

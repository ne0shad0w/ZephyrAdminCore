<?php

namespace ne0shad0w\ZephyrAdminCoreBundle\ZephyrAdminCoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * FosUser
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity
 */
class FosUser extends BaseUser
{
	    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

}

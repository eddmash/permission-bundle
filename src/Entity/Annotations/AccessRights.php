<?php

/**
 * This file is part of the permission bundle
 *
 *
 * Created by : Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
 * On Date : 1/16/19 3:06 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eddmash\PermissionBundle\Entity\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 *
 * @Target({"CLASS"})
 */
class AccessRights
{
    /**
     * @var array<string>
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public $permissions = [];

    /**
     * @Required
     * @var string
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public $label;
    /**
     * @Required
     * @var string
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public $tag;


    /**
     * @var string
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public $id;


    public $defaultPermissions = ['add', 'update', 'view', 'delete'];

    public function getPermissions()
    {
        if ($this->id) {
            $withID = [];
            foreach ($this->defaultPermissions as $defaultPermission) {
                $withID[] = sprintf("%s_%s", $defaultPermission, $this->id);
            }
            $this->defaultPermissions = $withID;
        }
        return array_merge($this->permissions, $this->defaultPermissions);
    }

}
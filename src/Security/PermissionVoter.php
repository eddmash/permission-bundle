<?php
/**
 * This file is part of the permission-bundle
 *
 *
 * Created by : Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
 * On Date : 1/15/19 2:20 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;


use App\Entity\User;
use Eddmash\PermissionBundle\Entity\AuthPermission;
use Eddmash\PermissionBundle\Repository\AuthPermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    private static $perms = [];
    /**
     * @var AuthPermissionRepository
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    private $permissionRepository;

    public function __construct(AuthPermissionRepository $permissionRepository)
    {

        $this->permissionRepository = $permissionRepository;
    }


    /**
     * @return AuthPermission[]
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public function getUserPermissions($user)
    {
        return $this->permissionRepository->findByUser($user);
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        // if it doesn't contain '-can_' we don't care for it
        return strpos($attribute, '_can_') > 0;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // clear previous one
        if (!array_key_exists($user->getId(), static::$perms)) {
            static::$perms = [];
        }
        if (!static::$perms) {
            // cache to avoid more queries
            $perms = $this->getUserPermissions($user) ?? [];
            $perms = array_map(function (AuthPermission $permission) {
                $name = $permission->getName();
                $name = str_replace("-", "_", $name);
                return $name;
            }, $perms);

            static::$perms[$user->getId()] = $perms ?? [];
        }

        return in_array($attribute, static::$perms[$user->getId()]);

    }
}
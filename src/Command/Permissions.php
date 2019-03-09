<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/9/19
 * Time: 8:24 PM
 */

namespace Eddmash\PermissionBundle\Command;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Eddmash\PermissionBundle\Entity\Annotations\AccessRights;
use Eddmash\PermissionBundle\Entity\AuthPermission;
use Eddmash\PermissionBundle\Entity\AuthRole;
use Symfony\Component\Console\Output\OutputInterface;

class Permissions
{

    /**
     * @var null|OutputInterface
     */
    private $output;
    /**
     * @var string
     */
    private $userEntity;
    /**
     * @var string
     */
    private $fetchAdminCallback;

    public function __construct(?OutputInterface $output = null,
                                string $userEntity, string $fetchAdminCallback)
    {
        $this->output = $output;
        $this->userEntity = $userEntity;
        $this->fetchAdminCallback = $fetchAdminCallback;
    }

    public function load(EntityManagerInterface $manager, AnnotationReader $annotationReader)
    {
        $this->write("setting up permissions");
        $rootRole = $manager->getRepository(AuthRole::class)
            ->findOneBy(array("name" => AuthRole::ROLE_ROOT));
        if (!$rootRole){
            $rootRole = new AuthRole();
            $rootRole->setName(AuthRole::ROLE_ROOT);
            $manager->persist($rootRole);
        }
        $meta = $manager->getMetadataFactory()->getAllMetadata();

        $connection = $manager->getConnection();
        $connection->query('DELETE FROM auth_permission');
        $connection->query('DELETE FROM auth_role_auth_permission');
        $connection->query('ALTER TABLE auth_permission AUTO_INCREMENT = 1');
        $connection->query('ALTER TABLE auth_role_auth_permission AUTO_INCREMENT = 1');
        foreach ($meta as $m) {

            /**@var  $accessAnnotation AccessRights */
            $accessAnnotation = $annotationReader->getClassAnnotation($m->getReflectionClass(),
                AccessRights::class);

            if ($accessAnnotation) {

                $tag = $accessAnnotation->tag;
                $this->write("setting up permissions for " . $accessAnnotation->tag);

                foreach ($accessAnnotation->getPermissions() as $permName) {
                    $perm = new AuthPermission();
                    $perm->setName(sprintf("%s-can_%s", $tag, $permName));
                    $perm->setLabel(sprintf("%s-can_%s", $accessAnnotation->label, $permName));
                    $perm->setDescription(sprintf("User can `%s` %s", $permName, $tag));
                    $perm->addRole($rootRole);

                    $allowed = $adminPermissions[$tag] ?? [];
                    if (in_array($permName, $allowed)) {
                        $rootRole->addPermission($perm);
                        $perm->addRole($rootRole);
                    }

                    $manager->persist($perm);

                }
            }
        }

        $repo = $manager->getRepository($this->userEntity);

        $fetchAdminCallback = $this->fetchAdminCallback;
        if (method_exists($repo, $fetchAdminCallback)) {
            $edd = call_user_func([$repo, $fetchAdminCallback]);
            $rootRole->addUser($edd);
            $manager->persist($edd);
        }

        $manager->persist($rootRole);
        $manager->flush();

        $this->write("Done  ");
    }

    private function write($string)
    {
        if ($this->output) {
            $this->output->writeln($string);
        } else {
            echo $string . PHP_EOL;
        }
    }
}
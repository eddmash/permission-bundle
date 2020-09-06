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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Eddmash\PermissionBundle\Entity\Annotations\AccessRights;
use Eddmash\PermissionBundle\Entity\AuthPermission;
use Eddmash\PermissionBundle\Entity\AuthRole;
use Gedmo\Tree\RepositoryInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
    private $input;
    /**
     * @var string
     *
     *
     * @author Eddilbert Macharia (http://eddmash.com) <edd.cowan@gmail.com>
     */
    private $username;

    public function __construct(string $userEntity,
                                string $fetchAdminCallback,
                                string $username,
                                ?InputInterface $input,
                                ?OutputInterface $output = null)
    {
        $this->output = $output;
        $this->userEntity = $userEntity;
        $this->fetchAdminCallback = $fetchAdminCallback;
        $this->input = $input;
        $this->username = $username;
    }

    public function load(EntityManagerInterface $manager, AnnotationReader $annotationReader)
    {
        /**
         * @var $repo EntityRepository
         */
        $repo = $manager->getRepository($this->userEntity);
        $adminuser = null;
        $fetchAdminCallback = $this->fetchAdminCallback;
        if (method_exists($repo, $fetchAdminCallback)) {
            $adminuser = call_user_func([$repo, $fetchAdminCallback]);
        }


        if (empty($adminuser) && !empty($this->username)) {

            $adminuserQ = $repo->createQueryBuilder('u')
                ->select("u")
                ->where("u.username = :username")
                ->setParameter("username", $this->username)
                ->getQuery();
            try {
                $adminuser = $adminuserQ->getSingleResult();
            } catch (NoResultException $e) {
                throw new \RuntimeException(
                    "An admin user with username '$this->username' does not exists, 
                    please use an existing user or
                     set 'eddmash_permission.fetch_admin_callback' ");

            } catch (NonUniqueResultException $e) {
            }

        }

        if (empty($adminuser)) {
            throw new \RuntimeException(
                "An admin user is required, please set 'eddmash_permission.fetch_admin_callback' ");
        }

        $this->write("setting up permissions");
        $rootRole = $manager->getRepository(AuthRole::class)
            ->findOneBy(array("name" => AuthRole::ROLE_ROOT));
        if (!$rootRole) {
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

        $rootRole->addUser($adminuser);
        $manager->persist($adminuser);
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
<?php

namespace Eddmash\PermissionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Eddmash\PermissionBundle\Entity\AuthPermission;
use Eddmash\PermissionBundle\Entity\AuthRole;

/**
 * @method AuthPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthPermission[]    findAll()
 * @method AuthPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthPermission::class);
    }

    /**
     * @return AuthPermission[]
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public function findByUser($user)
    {
        return $this->createQueryBuilder('a')
            ->join('a.roles', 'roles')
            ->join('roles.users', 'users')
            ->where('users = :val')
            ->setParameter('val', $user)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('auth_permission')
            ->orderBy('auth_permission.id');
    }

    public function findAllBuilderByRole(AuthRole $role): QueryBuilder
    {
        return $this->createQueryBuilder('auth_permission')
            ->join('auth_permission.roles', 'roles')
            ->where('roles = :role')
            ->setParameter("role", $role)
            ->orderBy('auth_permission.name');

    }

    public function deleteByRole($role)
    {
    }

    public function findAllByIds(array $ids)
    {
        $qb = $this->createQueryBuilder('p');
        return $this->createQueryBuilder('p')
            ->where($qb->expr()->in('p.id', $ids))->getQuery()
            ->getResult();
    }

    // /**
    //  * @return AuthPermission[] Returns an array of AuthPermission objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AuthPermission
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

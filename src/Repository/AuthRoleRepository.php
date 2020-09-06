<?php

namespace Eddmash\PermissionBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Eddmash\PermissionBundle\Entity\AuthRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @method AuthRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthRole[]    findAll()
 * @method AuthRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthRole::class);
    }

    public function findAllBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('auth_role')->orderBy('auth_role.id');
    }
    // /**
    //  * @return AuthRole[] Returns an array of AuthRole objects
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
    public function findOneBySomeField($value): ?AuthRole
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

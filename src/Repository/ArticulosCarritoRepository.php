<?php

namespace App\Repository;

use App\Entity\ArticulosCarrito;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticulosCarrito|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticulosCarrito|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticulosCarrito[]    findAll()
 * @method ArticulosCarrito[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticulosCarritoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticulosCarrito::class);
    }

    //    /**
    //     * @return Citas[] Returns an array of Citas objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Citas
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository;

use App\Entity\Pedidos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pedidos>
 */
class PedidosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedidos::class);
    }

    public function findAllWithProductos()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.productos', 'pr') // Suponiendo que la relación se llame "productos"
            ->addSelect('pr')
            ->getQuery()
            ->getResult();
    }
    /**
     * Obtiene los pedidos que tienen el estado null o vacío.
     *
     * @return Pedido[] Returns an array of Pedido objects
     */
    public function findPedidosPendientes(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.estado IS NULL OR p.estado = \'\'')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtiene los pedidos que tienen el estado completado.
     *
     * @return Pedido[] Returns an array of Pedido objects
     */
    public function findPedidosCompletados(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.estado = \'completado\'')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\ArticuloPedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticuloPedidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticuloPedido::class);
    }

    // Métodos personalizados del repositorio, si los necesitas
}

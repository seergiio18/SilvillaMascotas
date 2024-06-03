<?php

namespace App\Controller;

use App\Entity\Pedidos;
use App\Entity\ArticuloPedido;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AdminPedidosController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/admin/pedidos", name: "admin_pedidos")]
    public function index(Request $request, PaginatorInterface $paginator, ManagerRegistry $doctrine): Response
    {
        $page = $request->query->getInt('page', 1);
        
        // Obtener todos los pedidos, ordenados por fechaPedido en orden descendente
        $pedidosQuery = $doctrine->getRepository(Pedidos::class)
            ->createQueryBuilder('p')
            ->orderBy('p.fecha_pedido', 'DESC') // Ordenar por fechaPedido en orden descendente
            ->getQuery();
        
        // Paginar los pedidos
        $pagination = $paginator->paginate(
            $pedidosQuery, 
            $page, 
            10
        );
        
        // Obtener pedidos pendientes y completados
        $pedidosPendientes = $doctrine->getRepository(Pedidos::class)->findBy(['estado' => 'pendiente']);
        
        // Obtener pedidos completados, ordenados por fechaPedido en orden descendente
        $pedidosCompletados = $doctrine->getRepository(Pedidos::class)
            ->createQueryBuilder('p')
            ->where('p.estado = :estado')
            ->setParameter('estado', 'completado')
            ->orderBy('p.fecha_pedido', 'DESC') // Ordenar por fechaPedido en orden descendente
            ->getQuery()
            ->getResult();
    
        return $this->render('admin/pedidos/index.html.twig', [
            'pagination' => $pagination,
            'pedidosPendientes' => $pedidosPendientes,
            'pedidosCompletados' => $pedidosCompletados,
        ]);
    }

    #[Route("/admin/pedidos/{id}", name: "admin_pedidos_ver")]
    public function ver(int $id, ManagerRegistry $doctrine): Response
    {
        $pedido = $doctrine->getRepository(Pedidos::class)->find($id);
        $articulosPedido = $doctrine->getRepository(ArticuloPedido::class)->findBy(['pedido' => $pedido]);

        return $this->render('admin/pedidos/ver.html.twig', [
            'pedido' => $pedido,
            'articulosPedido' => $articulosPedido,
        ]);
    }

    #[Route("/admin/pedidos/{id}/completar", name: "admin_pedidos_completar")]
    public function completarPedido(Request $request, Pedidos $pedido, ManagerRegistry $doctrine): Response
    {
        // Marcar el pedido como completado
        $pedido->setEstado('completado');
        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        // Redireccionar a la página de detalles del pedido
        return $this->redirectToRoute('admin_pedidos_ver', ['id' => $pedido->getId()]);
    }


    #[Route("/admin/pedidos/{id}/eliminar", name: "admin_pedidos_eliminar", methods: ["POST"])]
    public function eliminar(Request $request, Pedidos $pedido): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pedido->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($pedido);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_pedidos');
    }
}
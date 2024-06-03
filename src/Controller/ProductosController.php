<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Entity\ArticuloPedido;
use App\Entity\ArticulosCarrito;
use App\Form\ProductosType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;


class ProductosController extends AbstractController
{
    private $entityManager;
    private $doctrine;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
    }

    #[Route("/admin/productos", name: "admin_productos")]
    public function index(Request $request, ManagerRegistry $doctrine, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);

        $entityManager = $doctrine->getManager();
        $productos = $entityManager->getRepository(Productos::class)->findAll();

        $productosRepository = $entityManager->getRepository(Productos::class);
        $productosQuery = $productosRepository->createQueryBuilder('p');
        // Obtener los productos paginados
        $pagination = $paginator->paginate(
            $productosQuery->getQuery(), // Consulta de productos
            $page, // Número de página actual
            10 // Cantidad de productos por página
        );
    
        return $this->render('admin/productos/index.html.twig', [
            'productos' => $productos,
            'pagination' => $pagination,
        ]);
    }

    #[Route("/admin/productos/nuevo", name: "admin_productos_nuevo")]
    public function nuevo(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $producto = new Productos();
        $form = $this->createForm(ProductosType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($producto);
            $entityManager->flush();

            return $this->redirectToRoute('admin_productos');
        }

        return $this->render('admin/productos/nuevo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/productos/{id}/editar", name: "admin_productos_editar")]
    public function editar(Request $request, Productos $producto, ManagerRegistry $doctrine): Response
{
    // Creamos el formulario y lo asociamos al producto
    $form = $this->createForm(ProductosType::class, $producto);

    // Establecemos los datos actuales en el formulario
    $form->setData($producto);

    // Manejamos la solicitud
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // El formulario maneja automáticamente la actualización de la entidad

        // Guardamos los cambios
        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        // Redireccionamos al usuario
        return $this->redirectToRoute('admin_productos');
    }

    return $this->render('admin/productos/editar.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route("/admin/productos/{id}/eliminar", name: "admin_productos_eliminar", methods: ["DELETE"])]
public function eliminar(Request $request, Productos $producto, EntityManagerInterface $entityManager): Response
{
    // Buscar y eliminar registros relacionados en la tabla articulo_pedido
    $articulosPedido = $entityManager->getRepository(ArticuloPedido::class)->findBy(['producto' => $producto]);
    foreach ($articulosPedido as $articuloPedido) {
        $entityManager->remove($articuloPedido);
    }

    // Buscar y eliminar registros relacionados en la tabla articulos_carrito
    $articulosCarrito = $entityManager->getRepository(ArticulosCarrito::class)->findBy(['producto' => $producto]);
    foreach ($articulosCarrito as $articuloCarrito) {
        $entityManager->remove($articuloCarrito);
    }

    // Eliminar el producto
    $entityManager->remove($producto);
    $entityManager->flush();

    // Redirigir a la página deseada después de eliminar el producto
    return $this->redirectToRoute('admin_productos');
}
}

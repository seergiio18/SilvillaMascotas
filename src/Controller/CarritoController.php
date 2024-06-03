<?php

namespace App\Controller;

use App\Entity\Carrito;
use App\Entity\ArticulosCarrito;
use App\Entity\Productos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CarritoController extends AbstractController
{
    #[Route('/carrito/agregar/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function agregarProducto($id, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $usuario = $security->getUser();
        $producto = $em->getRepository(Productos::class)->find($id);

        if (!$producto) {
            return new JsonResponse(['error' => 'Producto no encontrado'], 404);
        }

        $carrito = $em->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);

        if (!$carrito) {
            $carrito = new Carrito();
            $carrito->setUsuario($usuario);
            $carrito->setFechaPedido(new \DateTime());
            $em->persist($carrito);
            $em->flush();
        }

        $articuloCarrito = $em->getRepository(ArticulosCarrito::class)->findOneBy([
            'carrito' => $carrito,
            'producto' => $producto
        ]);

        if ($articuloCarrito) {
            $articuloCarrito->setCantidad($articuloCarrito->getCantidad() + 1);
        } else {
            $articuloCarrito = new ArticulosCarrito();
            $articuloCarrito->setCarrito($carrito);
            $articuloCarrito->setProducto($producto);
            $articuloCarrito->setCantidad(1); // Puedes ajustar la cantidad según sea necesario
        }

        $em->persist($articuloCarrito);
        $em->flush();

        return new JsonResponse(['success' => 'Producto agregado al carrito']);
    }


    #[Route('/carrito/update/{id}', name: 'update_cart', methods: ['POST'])]
    public function actualizarProducto($id, Request $request, EntityManagerInterface $em): Response
    {
        // Obtener el artículo del carrito que se va a actualizar
        $articuloCarrito = $em->getRepository(ArticulosCarrito::class)->find($id);

        if (!$articuloCarrito) {
            throw $this->createNotFoundException('Artículo del carrito no encontrado');
        }

        // Actualizar la cantidad del artículo del carrito
        $nuevaCantidad = $request->request->get('cantidad');
        $articuloCarrito->setCantidad($nuevaCantidad);

        $em->flush();

        return $this->redirectToRoute('carrito');
    }
    #[Route('/carrito/remove/{id}', name: 'carrito_remove', methods: ['GET'])]
    public function eliminarArticulo($id, EntityManagerInterface $em): Response
    {
        $articuloCarrito = $em->getRepository(ArticulosCarrito::class)->find($id);

        if (!$articuloCarrito) {
            throw $this->createNotFoundException('Artículo del carrito no encontrado');
        }

        $em->remove($articuloCarrito);
        $em->flush();

        return $this->redirectToRoute('carrito');
    }


    #[Route('/carrito', name: 'carrito')]
    public function mostrarCarrito(EntityManagerInterface $em, Security $security): Response
    {
        $usuario = $security->getUser();
        $carrito = $em->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);

        if (!$carrito) {
            return $this->render('user/carrito-vacio.html.twig');
        }

        $articulosCarrito = $em->getRepository(articulosCarrito::class)->findBy(['carrito' => $carrito]);

        return $this->render('user/carrito.html.twig', [
            'articulosCarrito' => $articulosCarrito,
        ]);
    }

    #[Route('/productos/{categoria}', name: 'productos_por_categoria')]
    public function productosPorCategoria(string $categoria,EntityManagerInterface $em, ManagerRegistry $doctrine, Security $security): Response
    {
        $entityManager = $doctrine->getManager();
        $productos = $entityManager->getRepository(productos::class)->findBy(['categoria' => $categoria]);

        $usuario = $security->getUser();
        $carrito = $em->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);
        $articulosCarrito = $em->getRepository(ArticulosCarrito::class)->findBy(['carrito' => $carrito]);

        return $this->render('user/productos-categoria.html.twig', [
            'productos' => $productos,
            'categoria' => $categoria,
            'articulosCarrito' => $articulosCarrito,
        ]);
    }
}

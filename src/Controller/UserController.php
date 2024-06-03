<?php

namespace App\Controller;

use App\Form\CitasType;
use App\Form\VetCitasType;
use App\Form\UserType;
use App\Entity\Citas;
use App\Entity\User;
use App\Entity\Pedidos;
use App\Entity\Productos;
use App\Entity\Carrito;
use App\Entity\ArticulosCarrito;
use App\Entity\ArticuloPedido;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;



class UserController extends AbstractController
{
    private $entityManager;
    private $doctrine;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
        $this->passwordHasher = $passwordHasher;
    }



    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/productos', name: 'productos')]
    public function productos(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $productos = $entityManager->getRepository(Productos::class)->findAll();

        return $this->render('user/productos.html.twig', [
            'productos' => $productos,
        ]);
    }

    #[Route('/producto/anadir/{id}', name: 'producto_anadir')]
    public function anadirAlCarrito(Productos $producto, ManagerRegistry $doctrine, Security $security): Response
    {
        $usuario = $security->getUser();
        $entityManager = $doctrine->getManager();
        $carrito = $entityManager->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);

        if (!$carrito) {
            $carrito = new Carrito();
            $carrito->setUsuario($usuario);
            $entityManager->persist($carrito);
            $entityManager->flush();
        }

        $articuloCarrito = new ArticulosCarrito();
        $articuloCarrito->setProducto($producto);
        $articuloCarrito->setCarrito($carrito);
        $articuloCarrito->setCantidad(1); // Puedes ajustar la cantidad según sea necesario

        $entityManager->persist($articuloCarrito);
        $entityManager->flush();

        return $this->redirectToRoute('carrito');
    }

    #[Route('/productos/total', name: 'productos_por_categoria')]
    public function productosPorCategoria(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, ManagerRegistry $doctrine, Security $security): Response
    {
        $page = $request->query->getInt('page', 1);
        $entityManager = $doctrine->getManager();
    
        $usuario = $security->getUser();
        $carrito = $em->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);
        $articulosCarrito = $em->getRepository(ArticulosCarrito::class)->findBy(['carrito' => $carrito]);
    
        $categoria = $request->query->get('categoria'); // Obtener la categoría desde la URL
        $nombre = $request->query->get('nombre'); // Obtener el nombre desde la URL
    
        $productosRepository = $entityManager->getRepository(Productos::class); // Pasa la clase Productos como argumento
    
        // Lógica para obtener productos filtrados basados en la categoría y el nombre recibidos
        $productosQuery = $productosRepository->createQueryBuilder('p');
    
        if ($categoria) {
            $productosQuery->andWhere('p.categoria = :categoria')
                ->setParameter('categoria', $categoria);
        }
    
        if ($nombre) {
            $productosQuery->andWhere('p.nombre LIKE :nombre')
                ->setParameter('nombre', '%'.$nombre.'%');
        }
    
        // Obtener los productos paginados
        $pagination = $paginator->paginate(
            $productosQuery->getQuery(), // Consulta de productos
            $page, // Número de página actual
            10 // Cantidad de productos por página
        );
    
        $totalCarrito = $this->calcularTotalCarrito($articulosCarrito);
    
        return $this->render('user/productos-categoria.html.twig', [
            'pagination' => $pagination,
            'articulosCarrito' => $articulosCarrito,
            'totalCarrito' => $totalCarrito,
        ]);
    }
    

    
    private function calcularTotalCarrito($articulosCarrito)
    {
        $total = 0;
        foreach ($articulosCarrito as $articulo) {
            $total += $articulo->getProducto()->getPrecio() * $articulo->getCantidad();
        }
        return $total;
    }

    #[Route('/peluqueria', name: 'peluqueria')]
    public function peluqueria(Request $request): Response
    {
        $citas = new Citas();
        $form = $this->createForm(CitasType::class, $citas);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($citas);
            $this->entityManager->flush();

            $this->addFlash('success', '¡Cita solicitada correctamente, te enviaremos un mensaje de confirmación!');

            return $this->redirectToRoute('peluqueria', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/peluqueria.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/veterinaria', name: 'veterinaria')]
    public function veterinaria(Request $request): Response
    {
        $citas = new Citas();
        $formVet = $this->createForm(VetCitasType::class, $citas);
        $formVet->handleRequest($request);

        if ($formVet->isSubmitted() && $formVet->isValid()) {
            $this->entityManager->persist($citas);
            $this->entityManager->flush();

            $this->addFlash('success', '¡Cita solicitada correctamente, te enviaremos un mensaje de confirmación!');

            return $this->redirectToRoute('veterinaria', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/veterinaria.html.twig', [
            'formVet' => $formVet->createView(),
        ]);
    }

    #[Route('/galeria', name: 'galeria')]
    public function blog(): Response
    {
        return $this->render('user/galeria.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/nosotros', name: 'nosotros')]
    public function nosotros(): Response
    {
        return $this->render('user/nosotros.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/contacto', name: 'contacto')]
    public function contacto(): Response
    {
        return $this->render('user/contacto.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/registro', name: 'registro')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User($this->passwordHasher);
        $registrationForm = $this->createForm(UserType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $user = $registrationForm->getData();
            $plainPassword = $registrationForm->get('plainPassword')->getData();

            // Hashing de la contraseña
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plainPassword
            );

            // Establecer la contraseña hasheada en el usuario
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('userRegistration');

            return $this->redirectToRoute('registro');
        }


        // Lógica de inicio de sesión
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'registration_form' => $registrationForm->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): RedirectResponse
    {
        return $this->redirectToRoute('registro');
    }


    #[Route('/profile', name: 'app_user_profile')]
    public function perfil(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        // Obtener los pedidos del usuario actual
        $entityManager = $doctrine->getManager();
        $userPedidos = $entityManager->getRepository(Pedidos::class)->findBy(['usuario' => $user]);

        return $this->render('user/perfil.html.twig', [
            'email' => $user->getEmail(),
            'pedidos' => $userPedidos,
        ]);
    }

    #[Route('/carrito', name: 'carrito')]
    public function mostrarCarrito(EntityManagerInterface $em, Security $security): Response
    {
        $usuario = $security->getUser();
        $carrito = $em->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);
    
        if (!$carrito) {
            return $this->render('carrito/vacio.html.twig'); // Crear una plantilla para carrito vacío
        }
    
        $articulosCarrito = $em->getRepository(ArticulosCarrito::class)->findBy(['carrito' => $carrito]);
    
        return $this->render('user/carrito.html.twig', [
            'articulosCarrito' => $articulosCarrito,
        ]);
    }

    #[Route("/pedido/crear", name: "pedido_crear")]
    public function crearPedido(EntityManagerInterface $em, Security $security): Response
    {
        $usuario = $security->getUser();
        $carrito = $em->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);
    
        if (!$carrito) {
            return $this->redirectToRoute('carrito'); // O manejar de otra manera si no hay carrito
        }
    
        $pedido = new Pedidos();
        $pedido->setUsuario($usuario);
        $pedido->setFechaPedido(new \DateTime());
    
        // Configurar el estado del pedido como pendiente
        $pedido->setEstado('pendiente');
    
        // Calcula el total del pedido
        $total = 0;
    
        // Transferir artículos del carrito al pedido
        foreach ($carrito->getArticulosCarrito() as $articuloCarrito) {
            $articuloPedido = new ArticuloPedido();
            $articuloPedido->setProducto($articuloCarrito->getProducto());
            $articuloPedido->setCantidad($articuloCarrito->getCantidad());
            $articuloPedido->setPrecio($articuloCarrito->getProducto()->getPrecio()); // Asumiendo que la entidad Productos tiene un campo precio
            $articuloPedido->setPedido($pedido);
    
            $total += $articuloCarrito->getCantidad() * $articuloCarrito->getProducto()->getPrecio();
    
            $em->persist($articuloPedido);
        }
    
        $pedido->setTotal($total);
    
        $em->persist($pedido);
        $em->flush();
    
        // Vaciar el carrito
        foreach ($carrito->getArticulosCarrito() as $articuloCarrito) {
            $em->remove($articuloCarrito);
        }
        $em->flush();
    
        // Configurar mensaje flash
        $this->addFlash('success', '¡Pedido realizado con éxito!');
    
        return $this->redirectToRoute('app_user_profile');
    }
    

    #[Route('/admin', name: 'admin')]
    #[IsGaranted("ROLE_ADMIN")]
    public function onlyAdmin(): Response
    {
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route("/perfil/pedidos/{id}", name: "pedidos_ver")]
    public function ver(int $id, ManagerRegistry $doctrine): Response
    {
        $pedido = $doctrine->getRepository(Pedidos::class)->find($id);
        $articulosPedido = $doctrine->getRepository(ArticuloPedido::class)->findBy(['pedido' => $pedido]);

        return $this->render('user/pedido.html.twig', [
            'pedido' => $pedido,
            'articulosPedido' => $articulosPedido,
        ]);
    }

}
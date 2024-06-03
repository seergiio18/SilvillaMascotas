<?php

namespace App\Controller;

use App\Entity\Contacto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;

class ContactoController extends AbstractController
{

    #[Route("/contacto", name:"user_contacto")]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $contacto = new Contacto();
            $contacto->setNombre($request->request->get('nombre'));
            $contacto->setApellido($request->request->get('apellido'));
            $contacto->setEmail($request->request->get('email'));
            $contacto->setTelefono($request->request->get('telefono'));
            $contacto->setDescripcion($request->request->get('descripcion'));

            $em->persist($contacto);
            $em->flush();

            // Añadir un mensaje flash
            $this->addFlash('success', 'Formulario enviado con éxito.');

            // Redirigir para evitar reenvíos del formulario
            return $this->redirectToRoute('user_contacto');
        }

        return $this->render('user/contacto.html.twig');
    }

    #[Route("/contacto/success", name:"contacto_success")]
    public function success(): Response
    {
        return new Response('Formulario enviado con éxito.');
    }

    #[Route("/admin/contacto", name:"admin_contacto")]
    public function verContacto(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        // Obtener la consulta para todos los contactos
        $query = $em->getRepository(Contacto::class)->createQueryBuilder('c')
            ->getQuery();

        // Configurar la paginación
        $pagination = $paginator->paginate(
            $query, // Consulta o QueryBuilder
            $request->query->getInt('page', 1), // Número de página, por defecto 1
            10 // Elementos por página
        );

        // Renderizar la plantilla y pasar los contactos paginados a la vista
        return $this->render('admin/contacto/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
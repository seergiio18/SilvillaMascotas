<?php

namespace App\Controller;

use App\Entity\Citas;
use App\Form\CitasType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;

class AdminCitasController extends AbstractController
{
    #[Route("/admin/citas", name: "admin_citas")]
    public function index(Request $request, ManagerRegistry $doctrine, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);

        $entityManager = $doctrine->getManager();
        $citasRepository = $entityManager->getRepository(Citas::class);
        $citasQuery = $citasRepository->createQueryBuilder('c')
            ->orderBy('c.fecha', 'DESC'); // Opcional: ordenar las citas por fecha descendente

        // Obtener las citas paginadas
        $pagination = $paginator->paginate(
            $citasQuery->getQuery(), // Consulta de citas
            $page, // Número de página actual
            10 // Cantidad de citas por página
        );

        return $this->render('admin/citas/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route("/admin/citas/{id}/editar", name: "admin_citas_editar")]
    public function editarCita(Request $request, Citas $cita, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CitasType::class, $cita);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('admin_citas');
        }

        return $this->render('admin/citas/editar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

/*     #[Route("/admin/citas/{id}/eliminar", name: "admin_citas_eliminar", methods: ["DELETE"])]
    public function eliminarCita(Request $request, Citas $cita, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cita->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($cita);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_citas');
    }   */
    
}

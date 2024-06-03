<?php

namespace App\Controller;

use App\Entity\Tiendas;
use App\Form\TiendasType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminTiendasController extends AbstractController
{
    private $entityManager;
    private $doctrine;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
    }

    #[Route("/admin/tiendas", name: "admin_tiendas")]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $tiendasQuery = $this->doctrine->getRepository(Tiendas::class)->createQueryBuilder('t')->getQuery();

        $pagination = $paginator->paginate(
            $tiendasQuery, 
            $page, 
            10
        );

        return $this->render('admin/tiendas/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route("/admin/tiendas/nuevo", name: "admin_tiendas_nuevo")]
    public function nuevo(Request $request): Response
    {
        $tienda = new Tiendas();
        $form = $this->createForm(TiendasType::class, $tienda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tienda);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_tiendas');
        }

        return $this->render('admin/tiendas/nuevo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/tiendas/{id}/editar", name: "admin_tiendas_editar")]
    public function editar(Request $request, Tiendas $tienda): Response
    {
        $form = $this->createForm(TiendasType::class, $tienda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_tiendas');
        }

        return $this->render('admin/tiendas/editar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/tiendas/{id}/eliminar", name: "admin_tiendas_eliminar", methods: ["POST"])]
    public function eliminar(Request $request, Tiendas $tienda): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tienda->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($tienda);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_tiendas');
    }
}
<?php

namespace App\Controller;

use App\Entity\Citas;
use App\Entity\Tiendas;
use App\Form\CitasType;
use App\Form\VetCitasType;
use App\Repository\CitasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/citas')]
class CitasController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/', name: 'app_citas_index', methods: ['GET'])]
    public function index(CitasRepository $citasRepository): Response
    {
        return $this->render('citas/index.html.twig', [
            'citas' => $citasRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_citas_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cita = new Citas();
        $form = $this->createForm(CitasType::class, $cita);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Obtener la tienda seleccionada del formulario
            $tienda = $form->get('tienda')->getData();

            // Establecer la tienda en la cita
            if ($tienda instanceof Tiendas) {
                $cita->setTienda($tienda);
            } else {
                // Manejar el caso si la tienda no existe
                throw $this->createNotFoundException('Tienda no encontrada');
            }

            $entityManager->persist($cita);
            $entityManager->flush();

            return $this->redirectToRoute('app_citas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('citas/new.html.twig', [
            'cita' => $cita,
            'form' => $form->createView(),
        ]);

    }

    #[Route('/new/vet', name: 'app_citas_new_vet', methods: ['GET', 'POST'])]
    public function newVet(Request $request): Response
    {
        $cita = new Citas();
        $formVet = $this->createForm(VetCitasType::class, $cita);
        $formVet->handleRequest($request);
    
        if ($formVet->isSubmitted() && $formVet->isValid()) {
            // Obtener la tienda seleccionada del formulario
            $tienda = $formVet->get('tienda')->getData();
    
            // Establecer la tienda en la cita
            if ($tienda instanceof Tiendas) {
                $cita->setTienda($tienda);
            } else {
                // Manejar el caso si la tienda no existe
                throw $this->createNotFoundException('Tienda no encontrada');
            }
    
            $this->entityManager->persist($cita);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_citas_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('citas/new_vet.html.twig', [
            'form' => $formVet->createView(),
        ]);
    }
    

    #[Route('/{id}', name: 'app_citas_show', methods: ['GET'])]
    public function show(Citas $cita): Response
    {
        return $this->render('citas/show.html.twig', [
            'cita' => $cita,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_citas_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Citas $cita, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CitasType::class, $cita);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_citas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('citas/edit.html.twig', [
            'cita' => $cita,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_citas_delete', methods: ['POST'])]
    public function delete(Request $request, Citas $cita, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cita->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($cita);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_citas_index', [], Response::HTTP_SEE_OTHER);
    }
}

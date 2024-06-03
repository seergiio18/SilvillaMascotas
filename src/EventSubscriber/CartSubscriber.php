<?php

namespace App\EventSubscriber;

use App\Entity\ArticulosCarrito;
use App\Entity\Carrito;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class CartSubscriber implements EventSubscriberInterface
{
    private $security;
    private $em;
    private $twig;

    public function __construct(Security $security, EntityManagerInterface $em, Environment $twig)
    {
        $this->security = $security;
        $this->em = $em;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        $usuario = $this->security->getUser();
        if (!$usuario) {
            return;
        }

        $carrito = $this->em->getRepository(Carrito::class)->findOneBy(['usuario' => $usuario]);
        if (!$carrito) {
            $totalCantidad = 0;
        } else {
            $articulosCarrito = $this->em->getRepository(ArticulosCarrito::class)->findBy(['carrito' => $carrito]);
            $totalCantidad = 0;
            foreach ($articulosCarrito as $item) {
                $totalCantidad += $item->getCantidad();
            }
        }

        $this->twig->addGlobal('totalCantidad', $totalCantidad);
    }
}


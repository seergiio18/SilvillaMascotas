<?php
namespace App\Entity;

use App\Repository\ArticulosCarritoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticulosCarritoRepository::class)]
class ArticulosCarrito
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Carrito::class, inversedBy: 'articulosCarrito')]
    #[ORM\JoinColumn(name: "carrito_id", referencedColumnName: "id")]
    private ?Carrito $carrito = null;

    #[ORM\ManyToOne(targetEntity: Productos::class)]
    #[ORM\JoinColumn(name: "producto_id", referencedColumnName: "id")]
    private ?Productos $producto = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarrito(): ?Carrito
    {
        return $this->carrito;
    }

    public function setCarrito(?Carrito $carrito): self
    {
        $this->carrito = $carrito;

        return $this;
    }

    public function getProducto(): ?Productos
    {
        return $this->producto;
    }

    public function setProducto(?Productos $producto): self
    {
        $this->producto = $producto;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }
}

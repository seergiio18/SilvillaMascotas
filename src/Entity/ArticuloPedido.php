<?php

namespace App\Entity;

use App\Repository\ArticuloPedidoRepository;
use Doctrine\DBAL\Types\Types; // Asegurarse de tener esta línea
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticuloPedidoRepository::class)]
class ArticuloPedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Pedidos::class, inversedBy: 'articulosPedido')]
    #[ORM\JoinColumn(name: "pedido_id", referencedColumnName: "id")]
    private ?Pedidos $pedido = null;

    #[ORM\ManyToOne(targetEntity: Productos::class)]
    #[ORM\JoinColumn(name: "producto_id", referencedColumnName: "id")]
    private ?Productos $producto = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precio = null;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedido(): ?Pedidos
    {
        return $this->pedido;
    }

    public function setPedido(?Pedidos $pedido): self
    {
        $this->pedido = $pedido;

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

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): self
    {
        $this->precio = $precio;

        return $this;
    }
}

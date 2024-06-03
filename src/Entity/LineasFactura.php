<?php

namespace App\Entity;

use App\Repository\LineasFacturaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineasFacturaRepository::class)]
class LineasFactura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Facturas::class)]
    #[ORM\JoinColumn(name: "id_factura", referencedColumnName: "id")]
    private ?Facturas $factura = null;

    #[ORM\ManyToOne(targetEntity: Productos::class)]
    #[ORM\JoinColumn(name: "id_producto", referencedColumnName: "id")]
    private ?Productos $producto = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precio_unitario = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_linea = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFactura(): ?Facturas
    {
        return $this->factura;
    }

    public function setFactura(?Facturas $factura): static
    {
        $this->factura = $factura;

        return $this;
    }

    public function getProducto(): ?Productos
    {
        return $this->producto;
    }

    public function setProducto(?Productos $producto): static
    {
        $this->producto = $producto;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecioUnitario(): ?string
    {
        return $this->precio_unitario;
    }

    public function setPrecioUnitario(string $precio_unitario): static
    {
        $this->precio_unitario = $precio_unitario;

        return $this;
    }

    public function getTotalLinea(): ?string
    {
        return $this->total_linea;
    }

    public function setTotalLinea(string $total_linea): static
    {
        $this->total_linea = $total_linea;

        return $this;
    }
}

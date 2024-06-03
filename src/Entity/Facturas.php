<?php

namespace App\Entity;

use App\Repository\FacturasRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturasRepository::class)]
class Facturas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $numero_factura = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_emision = null;

    #[ORM\ManyToOne(targetEntity: Proveedores::class)]
    #[ORM\JoinColumn(name: "id_proveedor", referencedColumnName: "id")]
    private ?Proveedores $proveedor = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = null;

    #[ORM\Column]
    private ?int $id_tienda = null;

    #[ORM\ManyToOne(targetEntity: Tiendas::class)]
    #[ORM\JoinColumn(name: "id_tienda", referencedColumnName: "id")]
    private ?Tiendas $tienda = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroFactura(): ?string
    {
        return $this->numero_factura;
    }

    public function setNumeroFactura(string $numero_factura): static
    {
        $this->numero_factura = $numero_factura;

        return $this;
    }

    public function getFechaEmision(): ?\DateTimeInterface
    {
        return $this->fecha_emision;
    }

    public function setFechaEmision(\DateTimeInterface $fecha_emision): static
    {
        $this->fecha_emision = $fecha_emision;

        return $this;
    }

    public function getProveedor(): ?Proveedores
    {
        return $this->proveedor;
    }

    public function setProveedor(?Proveedores $proveedor): static
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getTienda(): ?Tiendas
    {
        return $this->tienda;
    }

    public function setTienda(?Tiendas $tienda): static
    {
        $this->tienda = $tienda;

        return $this;
    }
}


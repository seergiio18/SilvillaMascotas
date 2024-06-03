<?php
namespace App\Entity;

use App\Repository\CitasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitasRepository::class)]
class Citas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tiendas::class)]
    #[ORM\JoinColumn(name: "id_tienda", referencedColumnName: "id")]
    private ?Tiendas $tienda = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 100)]
    private ?string $servicio = null;

    #[ORM\Column(length: 100)]
    private ?string $cliente = null;

    #[ORM\Column(length: 20)]
    private ?string $telefono = null;

    #[ORM\Column(length: 100)]
    private ?string $horas = null;

    #[ORM\Column(type: "text")]
    private ?string $descripcion = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getServicio(): ?string
    {
        return $this->servicio;
    }

    public function setServicio(string $servicio): static
    {
        $this->servicio = $servicio;

        return $this;
    }

    public function getCliente(): ?string
    {
        return $this->cliente;
    }

    public function setCliente(string $cliente): static
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getHoras(): ?string
    {
        return $this->horas;
    }

    public function setHoras(?string $horas): static
    {
        $this->horas = $horas;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }


}

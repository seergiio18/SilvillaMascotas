<?php
namespace App\Entity;

use App\Repository\CarritoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarritoRepository::class)]
class Carrito
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?User $usuario = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $fechaPedido = null;

    #[ORM\OneToMany(targetEntity: ArticulosCarrito::class, mappedBy: 'carrito', cascade: ['persist', 'remove'])]
    private Collection $articulosCarrito;

    public function __construct()
    {
        $this->articulosCarrito = new ArrayCollection();
    }

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getFechaPedido(): ?\DateTimeInterface
    {
        return $this->fechaPedido;
    }

    public function setFechaPedido(?\DateTimeInterface $fechaPedido): self
    {
        $this->fechaPedido = $fechaPedido;

        return $this;
    }

    /**
     * @return Collection|ArticulosCarrito[]
     */
    public function getArticulosCarrito(): Collection
    {
        return $this->articulosCarrito;
    }

    public function addArticuloCarrito(ArticulosCarrito $articuloCarrito): self
    {
        if (!$this->articulosCarrito->contains($articuloCarrito)) {
            $this->articulosCarrito[] = $articuloCarrito;
            $articuloCarrito->setCarrito($this);
        }

        return $this;
    }

    public function removeArticuloCarrito(ArticulosCarrito $articuloCarrito): self
    {
        if ($this->articulosCarrito->removeElement($articuloCarrito)) {
            // set the owning side to null (unless already changed)
            if ($articuloCarrito->getCarrito() === $this) {
                $articuloCarrito->setCarrito(null);
            }
        }

        return $this;
    }
}

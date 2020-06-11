<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EquipoCorresponsalesRepository")
 */
class EquipoCorresponsales
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codigo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Corresponsal", mappedBy="equipo")
     */
    private $corresponsals;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Corresponsal", inversedBy="equipoCorresponsales")
     */
    private $CorresponsalCoordinador;

    /**
     * @ORM\Column(type="boolean")
     */
    private $es_activo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CantMiembrosEquipo", inversedBy="equipoCorresponsales")
     */
    private $cant_miembros;

    public function __construct()
    {
        $this->corresponsals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * @return Collection|Corresponsal[]
     */
    public function getCorresponsals(): Collection
    {
        return $this->corresponsals;
    }

    public function addCorresponsal(Corresponsal $corresponsal): self
    {
        if (!$this->corresponsals->contains($corresponsal)) {
            $this->corresponsals[] = $corresponsal;
            $corresponsal->addEquipo($this);
        }

        return $this;
    }

    /**
     * @param ArrayCollection $corresponsals
     */
    public function setCorresponsals(ArrayCollection $corresponsals): void
    {
        $this->corresponsals = $corresponsals;
    }



    public function removeCorresponsal(Corresponsal $corresponsal): self
    {
        if ($this->corresponsals->contains($corresponsal)) {
            $this->corresponsals->removeElement($corresponsal);
            $corresponsal->removeEquipo($this);
        }

        return $this;
    }

    public function getCorresponsalCoordinador(): ?Corresponsal
    {
        return $this->CorresponsalCoordinador;
    }

    public function setCorresponsalCoordinador(?Corresponsal $CorresponsalCoordinador): self
    {
        $this->CorresponsalCoordinador = $CorresponsalCoordinador;

        return $this;
    }

    public function getEsActivo(): ?bool
    {
        return $this->es_activo;
    }

    public function setEsActivo(bool $es_activo): self
    {
        $this->es_activo = $es_activo;

        return $this;
    }

    public function getCantMiembros(): ?CantMiembrosEquipo
    {
        return $this->cant_miembros;
    }

    public function setCantMiembros(?CantMiembrosEquipo $cant_miembros): self
    {
        $this->cant_miembros = $cant_miembros;

        return $this;
    }
}

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Corresponsal", mappedBy="equipo", cascade={"persist"})
     */
    private $corresponsals;


    /**
     * @ORM\Column(type="integer")
     */
    private $cantidadMiembros;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Corresponsal", inversedBy="equipoCorresponsales")
     */
    private $CorresponsalCoordinador;

    /**
     * @ORM\Column(type="boolean")
     */
    private $es_activo;

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



    public function getCantidadMiembros(): ?int
    {
        return $this->cantidadMiembros;
    }

    public function setCantidadMiembros(int $cantidadMiembros): self
    {
        $this->cantidadMiembros = $cantidadMiembros;

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
}

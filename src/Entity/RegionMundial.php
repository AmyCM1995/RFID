<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionMundialRepository")
 */
class RegionMundial
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="float")
     */
    private $tarifa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaisCorrespondencia", mappedBy="region")
     */
    private $paisCorrespondencias;

    public function __construct()
    {
        $this->paisCorrespondencias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getTarifa(): ?float
    {
        return $this->tarifa;
    }

    public function setTarifa(float $tarifa): self
    {
        $this->tarifa = $tarifa;

        return $this;
    }

    /**
     * @return Collection|PaisCorrespondencia[]
     */
    public function getPaisCorrespondencias(): Collection
    {
        return $this->paisCorrespondencias;
    }

    public function addPaisCorrespondencia(PaisCorrespondencia $paisCorrespondencia): self
    {
        if (!$this->paisCorrespondencias->contains($paisCorrespondencia)) {
            $this->paisCorrespondencias[] = $paisCorrespondencia;
            $paisCorrespondencia->setRegion($this);
        }

        return $this;
    }

    public function removePaisCorrespondencia(PaisCorrespondencia $paisCorrespondencia): self
    {
        if ($this->paisCorrespondencias->contains($paisCorrespondencia)) {
            $this->paisCorrespondencias->removeElement($paisCorrespondencia);
            // set the owning side to null (unless already changed)
            if ($paisCorrespondencia->getRegion() === $this) {
                $paisCorrespondencia->setRegion(null);
            }
        }

        return $this;
    }
}

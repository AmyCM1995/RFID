<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProvinciaCubaRepository")
 */
class ProvinciaCuba
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
    private $nombre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FechasNoCorrespondencia", mappedBy="provincia")
     */
    private $fechasNoCorrespondencias;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $es_activo;

    public function __construct()
    {
        $this->fechasNoCorrespondencias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection|FechasNoCorrespondencia[]
     */
    public function getFechasNoCorrespondencias(): Collection
    {
        return $this->fechasNoCorrespondencias;
    }

    public function addFechasNoCorrespondencia(FechasNoCorrespondencia $fechasNoCorrespondencia): self
    {
        if (!$this->fechasNoCorrespondencias->contains($fechasNoCorrespondencia)) {
            $this->fechasNoCorrespondencias[] = $fechasNoCorrespondencia;
            $fechasNoCorrespondencia->addProvincium($this);
        }

        return $this;
    }

    public function removeFechasNoCorrespondencia(FechasNoCorrespondencia $fechasNoCorrespondencia): self
    {
        if ($this->fechasNoCorrespondencias->contains($fechasNoCorrespondencia)) {
            $this->fechasNoCorrespondencias->removeElement($fechasNoCorrespondencia);
            $fechasNoCorrespondencia->removeProvincium($this);
        }

        return $this;
    }

    public function getEsActivo(): ?bool
    {
        return $this->es_activo;
    }

    public function setEsActivo(?bool $es_activo): self
    {
        $this->es_activo = $es_activo;

        return $this;
    }
}

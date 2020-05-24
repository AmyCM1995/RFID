<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SitioLectorRepository")
 */
class SitioLector
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lector", mappedBy="sitio")
     */
    private $lectors;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaisCorrespondencia", inversedBy="sitioLectors")
     */
    private $pais;

    public function __construct()
    {
        $this->lectors = new ArrayCollection();
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
     * @return Collection|Lector[]
     */
    public function getLectors(): Collection
    {
        return $this->lectors;
    }

    public function addLector(Lector $lector): self
    {
        if (!$this->lectors->contains($lector)) {
            $this->lectors[] = $lector;
            $lector->setSitio($this);
        }

        return $this;
    }

    public function removeLector(Lector $lector): self
    {
        if ($this->lectors->contains($lector)) {
            $this->lectors->removeElement($lector);
            // set the owning side to null (unless already changed)
            if ($lector->getSitio() === $this) {
                $lector->setSitio(null);
            }
        }

        return $this;
    }

    public function getPais(): ?PaisCorrespondencia
    {
        return $this->pais;
    }

    public function setPais(?PaisCorrespondencia $pais): self
    {
        $this->pais = $pais;

        return $this;
    }
}

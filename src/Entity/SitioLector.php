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
     * @ORM\ManyToOne(targetEntity="App\Entity\Area", inversedBy="sitioLectors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lector", mappedBy="sitio")
     */
    private $lectors;

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

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

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
}

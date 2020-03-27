<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LectorRepository")
 */
class Lector
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=18)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $proposito;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SitioLector", inversedBy="lectors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sitio;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lectura", mappedBy="lector")
     */
    private $lecturas;

    public function __construct()
    {
        $this->lecturas = new ArrayCollection();
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

    public function getProposito(): ?string
    {
        return $this->proposito;
    }

    public function setProposito(?string $proposito): self
    {
        $this->proposito = $proposito;

        return $this;
    }

    public function getSitio(): ?SitioLector
    {
        return $this->sitio;
    }

    public function setSitio(?SitioLector $sitio): self
    {
        $this->sitio = $sitio;

        return $this;
    }

    /**
     * @return Collection|Lectura[]
     */
    public function getLecturas(): Collection
    {
        return $this->lecturas;
    }

    public function addLectura(Lectura $lectura): self
    {
        if (!$this->lecturas->contains($lectura)) {
            $this->lecturas[] = $lectura;
            $lectura->setLector($this);
        }

        return $this;
    }

    public function removeLectura(Lectura $lectura): self
    {
        if ($this->lecturas->contains($lectura)) {
            $this->lecturas->removeElement($lectura);
            // set the owning side to null (unless already changed)
            if ($lectura->getLector() === $this) {
                $lectura->setLector(null);
            }
        }

        return $this;
    }
}

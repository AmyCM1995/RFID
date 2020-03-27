<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AreaRepository")
 */
class Area
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ciudad", inversedBy="areas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ciudad;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SitioLector", mappedBy="area")
     */
    private $sitioLectors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Envio", mappedBy="area_origen")
     */
    private $envios;

    public function __construct()
    {
        $this->sitioLectors = new ArrayCollection();
        $this->envios = new ArrayCollection();
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

    public function getCiudad(): ?Ciudad
    {
        return $this->ciudad;
    }

    public function setCiudad(?Ciudad $ciudad): self
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * @return Collection|SitioLector[]
     */
    public function getSitioLectors(): Collection
    {
        return $this->sitioLectors;
    }

    public function addSitioLector(SitioLector $sitioLector): self
    {
        if (!$this->sitioLectors->contains($sitioLector)) {
            $this->sitioLectors[] = $sitioLector;
            $sitioLector->setArea($this);
        }

        return $this;
    }

    public function removeSitioLector(SitioLector $sitioLector): self
    {
        if ($this->sitioLectors->contains($sitioLector)) {
            $this->sitioLectors->removeElement($sitioLector);
            // set the owning side to null (unless already changed)
            if ($sitioLector->getArea() === $this) {
                $sitioLector->setArea(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Envio[]
     */
    public function getEnvios(): Collection
    {
        return $this->envios;
    }

    public function addEnvio(Envio $envio): self
    {
        if (!$this->envios->contains($envio)) {
            $this->envios[] = $envio;
            $envio->setAreaOrigen($this);
        }

        return $this;
    }

    public function removeEnvio(Envio $envio): self
    {
        if ($this->envios->contains($envio)) {
            $this->envios->removeElement($envio);
            // set the owning side to null (unless already changed)
            if ($envio->getAreaOrigen() === $this) {
                $envio->setAreaOrigen(null);
            }
        }

        return $this;
    }
}

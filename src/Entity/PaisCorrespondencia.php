<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaisCorrespondenciaRepository")
 */
class PaisCorrespondencia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $es_activo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RegionMundial", inversedBy="paisCorrespondencias")
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ciudad", mappedBy="pais")
     */
    private $ciudades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SitioLector", mappedBy="pais_id")
     */
    private $sitioLectors;

    public function __construct()
    {
        $this->ciudades = new ArrayCollection();
        $this->sitioLectors = new ArrayCollection();
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

    public function getEsActivo(): ?bool
    {
        return $this->es_activo;
    }

    public function setEsActivo(bool $es_activo): self
    {
        $this->es_activo = $es_activo;

        return $this;
    }

    public function getRegion(): ?RegionMundial
    {
        return $this->region;
    }

    public function setRegion(?RegionMundial $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|Ciudad[]
     */
    public function getCiudades(): Collection
    {
        return $this->ciudades;
    }

    public function addCiudade(Ciudad $ciudade): self
    {
        if (!$this->ciudades->contains($ciudade)) {
            $this->ciudades[] = $ciudade;
            $ciudade->setPais($this);
        }

        return $this;
    }

    public function removeCiudade(Ciudad $ciudade): self
    {
        if ($this->ciudades->contains($ciudade)) {
            $this->ciudades->removeElement($ciudade);
            // set the owning side to null (unless already changed)
            if ($ciudade->getPais() === $this) {
                $ciudade->setPais(null);
            }
        }

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
            $sitioLector->setPaisId($this);
        }

        return $this;
    }

    public function removeSitioLector(SitioLector $sitioLector): self
    {
        if ($this->sitioLectors->contains($sitioLector)) {
            $this->sitioLectors->removeElement($sitioLector);
            // set the owning side to null (unless already changed)
            if ($sitioLector->getPaisId() === $this) {
                $sitioLector->setPaisId(null);
            }
        }

        return $this;
    }



}

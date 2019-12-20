<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CorresponsalRepository")
 */
class Corresponsal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $correo;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EquipoCorresponsales", inversedBy="corresponsals", cascade={"persist"})
     */
    private $equipo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EquipoCorresponsales", mappedBy="CorresponsalCoordinador", cascade={"persist"})
     */
    private $equipoCorresponsales;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $es_activo;



    public function __construct()
    {
        $this->equipo = new ArrayCollection();
        $this->equipoCorresponsales = new ArrayCollection();
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

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(?string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * @return Collection|EquipoCorresponsales[]
     */
    public function getEquipo(): Collection
    {
        return $this->equipo;
    }

    public function addEquipo(EquipoCorresponsales $equipo): self
    {
        if (!$this->equipo->contains($equipo)) {
            $this->equipo[] = $equipo;
        }

        return $this;
    }

    public function removeEquipo(EquipoCorresponsales $equipo): self
    {
        if ($this->equipo->contains($equipo)) {
            $this->equipo->removeElement($equipo);
        }

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * @return Collection|EquipoCorresponsales[]
     */
    public function getEquipoCorresponsales(): Collection
    {
        return $this->equipoCorresponsales;
    }

    public function addEquipoCorresponsale(EquipoCorresponsales $equipoCorresponsale): self
    {
        if (!$this->equipoCorresponsales->contains($equipoCorresponsale)) {
            $this->equipoCorresponsales[] = $equipoCorresponsale;
            $equipoCorresponsale->setCorresponsalCoordinador($this);
        }

        return $this;
    }

    public function removeEquipoCorresponsale(EquipoCorresponsales $equipoCorresponsale): self
    {
        if ($this->equipoCorresponsales->contains($equipoCorresponsale)) {
            $this->equipoCorresponsales->removeElement($equipoCorresponsale);
            // set the owning side to null (unless already changed)
            if ($equipoCorresponsale->getCorresponsalCoordinador() === $this) {
                $equipoCorresponsale->setCorresponsalCoordinador(null);
            }
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

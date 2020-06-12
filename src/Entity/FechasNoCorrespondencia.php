<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FechasNoCorrespondenciaRepository")
 */
class FechasNoCorrespondencia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $es_anual;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CumplimientoPlan", mappedBy="fecha_no_correspondencia")
     */
    private $cumplimientoPlans;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProvinciaCuba", inversedBy="fechasNoCorrespondencias")
     */
    private $provincia;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaInicio;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaFin;

    public function __construct()
    {
        $this->cumplimientoPlans = new ArrayCollection();
        $this->provincia = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEsAnual(): ?bool
    {
        return $this->es_anual;
    }

    public function setEsAnual(bool $es_anual): self
    {
        $this->es_anual = $es_anual;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection|CumplimientoPlan[]
     */
    public function getCumplimientoPlans(): Collection
    {
        return $this->cumplimientoPlans;
    }

    public function addCumplimientoPlan(CumplimientoPlan $cumplimientoPlan): self
    {
        if (!$this->cumplimientoPlans->contains($cumplimientoPlan)) {
            $this->cumplimientoPlans[] = $cumplimientoPlan;
            $cumplimientoPlan->setFechaNoCorrespondencia($this);
        }

        return $this;
    }

    public function removeCumplimientoPlan(CumplimientoPlan $cumplimientoPlan): self
    {
        if ($this->cumplimientoPlans->contains($cumplimientoPlan)) {
            $this->cumplimientoPlans->removeElement($cumplimientoPlan);
            // set the owning side to null (unless already changed)
            if ($cumplimientoPlan->getFechaNoCorrespondencia() === $this) {
                $cumplimientoPlan->setFechaNoCorrespondencia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProvinciaCuba[]
     */
    public function getProvincia(): Collection
    {
        return $this->provincia;
    }

    public function addProvincium(ProvinciaCuba $provincium): self
    {
        if (!$this->provincia->contains($provincium)) {
            $this->provincia[] = $provincium;
        }

        return $this;
    }

    public function removeProvincium(ProvinciaCuba $provincium): self
    {
        if ($this->provincia->contains($provincium)) {
            $this->provincia->removeElement($provincium);
        }

        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(?\DateTimeInterface $fechaFin): self
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }
}

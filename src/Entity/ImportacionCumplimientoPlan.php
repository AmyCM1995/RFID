<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImportacionCumplimientoPlanRepository")
 */
class ImportacionCumplimientoPlan
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CumplimientoPlan", mappedBy="importado")
     */
    private $cumplimientoPlans;

    public function __construct()
    {
        $this->cumplimientoPlans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

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
            $cumplimientoPlan->setImportado($this);
        }

        return $this;
    }

    public function removeCumplimientoPlan(CumplimientoPlan $cumplimientoPlan): self
    {
        if ($this->cumplimientoPlans->contains($cumplimientoPlan)) {
            $this->cumplimientoPlans->removeElement($cumplimientoPlan);
            // set the owning side to null (unless already changed)
            if ($cumplimientoPlan->getImportado() === $this) {
                $cumplimientoPlan->setImportado(null);
            }
        }

        return $this;
    }
}

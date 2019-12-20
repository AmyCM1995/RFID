<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CumplimientoPlanRepository")
 */
class CumplimientoPlan
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
    private $fecha_real;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FechasNoCorrespondencia", inversedBy="cumplimientoPlans")
     */
    private $fecha_no_correspondencia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaReal(): ?\DateTimeInterface
    {
        return $this->fecha_real;
    }

    public function setFechaReal(\DateTimeInterface $fecha_real): self
    {
        $this->fecha_real = $fecha_real;

        return $this;
    }

    public function getFechaNoCorrespondencia(): ?FechasNoCorrespondencia
    {
        return $this->fecha_no_correspondencia;
    }

    public function setFechaNoCorrespondencia(?FechasNoCorrespondencia $fecha_no_correspondencia): self
    {
        $this->fecha_no_correspondencia = $fecha_no_correspondencia;

        return $this;
    }
}

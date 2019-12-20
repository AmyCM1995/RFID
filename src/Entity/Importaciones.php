<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImportacionesRepository")
 */
class Importaciones
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
    private $fecha_importado;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fecha_inicio_plan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fecha_fin_plan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ciclo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dimension;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaImportado(): ?\DateTimeInterface
    {
        return $this->fecha_importado;
    }

    public function setFechaImportado(\DateTimeInterface $fecha_importado): self
    {
        $this->fecha_importado = $fecha_importado;

        return $this;
    }

    public function getFechaInicioPlan(): ?string
    {
        return $this->fecha_inicio_plan;
    }

    public function setFechaInicioPlan(string $fecha_inicio_plan): self
    {
        $this->fecha_inicio_plan = $fecha_inicio_plan;

        return $this;
    }

    public function getFechaFinPlan(): ?string
    {
        return $this->fecha_fin_plan;
    }

    public function setFechaFinPlan(string $fecha_fin_plan): self
    {
        $this->fecha_fin_plan = $fecha_fin_plan;

        return $this;
    }

    public function getCiclo(): ?string
    {
        return $this->ciclo;
    }

    public function setCiclo(string $ciclo): self
    {
        $this->ciclo = $ciclo;

        return $this;
    }

    public function getDimension(): ?string
    {
        return $this->dimension;
    }

    public function setDimension(string $dimension): self
    {
        $this->dimension = $dimension;

        return $this;
    }



}

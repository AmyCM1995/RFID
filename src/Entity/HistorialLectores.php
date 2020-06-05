<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistorialLectoresRepository")
 */
class HistorialLectores
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=600)
     */
    private $resultado;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IPLectorCubano", inversedBy="historialLectores")
     */
    private $ipLector;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_hora;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResultado(): ?string
    {
        return $this->resultado;
    }

    public function setResultado(string $resultado): self
    {
        $this->resultado = $resultado;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getIpLector(): ?IPLectorCubano
    {
        return $this->ipLector;
    }

    public function setIpLector(?IPLectorCubano $ipLector): self
    {
        $this->ipLector = $ipLector;

        return $this;
    }

    public function getFechaHora(): ?\DateTimeInterface
    {
        return $this->fecha_hora;
    }

    public function setFechaHora(\DateTimeInterface $fecha_hora): self
    {
        $this->fecha_hora = $fecha_hora;

        return $this;
    }
}

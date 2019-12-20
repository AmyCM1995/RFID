<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanImposicionCsvRepository")
 */
class PlanImposicionCsv
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fecha_dia;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $envio_1_1;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $envio_1_2;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $envio_2_1;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $envio_2_2;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $envio_3_1;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $envio_3_2;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaDia(): ?string
    {
        return $this->fecha_dia;
    }

    public function setFechaDia(string $fecha_dia): self
    {
        $this->fecha_dia = $fecha_dia;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEnvio11(): ?string
    {
        return $this->envio_1_1;
    }

    public function setEnvio11(?string $envio_1_1): self
    {
        $this->envio_1_1 = $envio_1_1;

        return $this;
    }

    public function getEnvio12(): ?string
    {
        return $this->envio_1_2;
    }

    public function setEnvio12(?string $envio_1_2): self
    {
        $this->envio_1_2 = $envio_1_2;

        return $this;
    }

    public function getEnvio21(): ?string
    {
        return $this->envio_2_1;
    }

    public function setEnvio21(?string $envio_2_1): self
    {
        $this->envio_2_1 = $envio_2_1;

        return $this;
    }

    public function getEnvio22(): ?string
    {
        return $this->envio_2_2;
    }

    public function setEnvio22(?string $envio_2_2): self
    {
        $this->envio_2_2 = $envio_2_2;

        return $this;
    }

    public function getEnvio31(): ?string
    {
        return $this->envio_3_1;
    }

    public function setEnvio31(?string $envio_3_1): self
    {
        $this->envio_3_1 = $envio_3_1;

        return $this;
    }

    public function getEnvio32(): ?string
    {
        return $this->envio_3_2;
    }

    public function setEnvio32(?string $envio_3_2): self
    {
        $this->envio_3_2 = $envio_3_2;

        return $this;
    }
}

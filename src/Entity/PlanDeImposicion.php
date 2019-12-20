<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanDeImposicionRepository")
 */
class PlanDeImposicion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $cod_envio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaisCorrespondencia", inversedBy="planDeImposicions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $cod_pais;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $importacion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Corresponsal")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cod_corresponsal;

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

    public function getCodEnvio(): ?string
    {
        return $this->cod_envio;
    }

    public function setCodEnvio(string $cod_envio): self
    {
        $this->cod_envio = $cod_envio;

        return $this;
    }

    public function getCodPais(): ?PaisCorrespondencia
    {
        return $this->cod_pais;
    }

    public function setCodPais(?PaisCorrespondencia $cod_pais): self
    {
        $this->cod_pais = $cod_pais;

        return $this;
    }

    public function getImportacion(): ?Importaciones
    {
        return $this->importacion;
    }

    public function setImportacion(?Importaciones $importacion): self
    {
        $this->importacion = $importacion;

        return $this;
    }

    public function getCodCorresponsal(): ?Corresponsal
    {
        return $this->cod_corresponsal;
    }

    public function setCodCorresponsal(?Corresponsal $cod_corresponsal): self
    {
        $this->cod_corresponsal = $cod_corresponsal;

        return $this;
    }



}

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

    /**
     * @ORM\Column(type="boolean")
     */
    private $es_enTiempo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codigo_envio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $id_transpondedor;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PlanDeImposicion", inversedBy="cumplimientoPlan", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_plan_imposicion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ImportacionCumplimientoPlan", inversedBy="cumplimientoPlans")
     */
    private $importado;

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

    public function getEsEnTiempo(): ?bool
    {
        return $this->es_enTiempo;
    }

    public function setEsEnTiempo(bool $es_enTiempo): self
    {
        $this->es_enTiempo = $es_enTiempo;

        return $this;
    }

    public function getCodigoEnvio(): ?string
    {
        return $this->codigo_envio;
    }

    public function setCodigoEnvio(string $codigo_envio): self
    {
        $this->codigo_envio = $codigo_envio;

        return $this;
    }

    public function getIdTranspondedor(): ?string
    {
        return $this->id_transpondedor;
    }

    public function setIdTranspondedor(string $id_transpondedor): self
    {
        $this->id_transpondedor = $id_transpondedor;

        return $this;
    }

    public function getIdPlanImposicion(): ?PlanDeImposicion
    {
        return $this->id_plan_imposicion;
    }

    public function setIdPlanImposicion(PlanDeImposicion $id_plan_imposicion): self
    {
        $this->id_plan_imposicion = $id_plan_imposicion;

        return $this;
    }

    public function getImportado(): ?ImportacionCumplimientoPlan
    {
        return $this->importado;
    }

    public function setImportado(?ImportacionCumplimientoPlan $importado): self
    {
        $this->importado = $importado;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LecturaRepository")
 */
class Lectura
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
    private $fecha_hora;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dia;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $validada;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $valida;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $es_marcado_como_terminal_dues;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $es_primero_calcular_HTD;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codigo_lectura_borrada;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detalle_lectura_borrada;

    /**
     * @ORM\Column(type="integer")
     */
    private $ctd_lecturas_luego_entregado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $tiene_lecturas_marcadas_como_TD;

    /**
     * @ORM\Column(type="integer")
     */
    private $cant_lecturas_entre_enviado_y_recibido;

    /**
     * @ORM\Column(type="integer")
     */
    private $cant_lecturas_despues_recibido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lector", inversedBy="lecturas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lector;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Envio", inversedBy="lecturas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $envio;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDia(): ?string
    {
        return $this->dia;
    }

    public function setDia(string $dia): self
    {
        $this->dia = $dia;

        return $this;
    }

    public function getValidada(): ?bool
    {
        return $this->validada;
    }

    public function setValidada(?bool $validada): self
    {
        $this->validada = $validada;

        return $this;
    }

    public function getValida(): ?bool
    {
        return $this->valida;
    }

    public function setValida(?bool $valida): self
    {
        $this->valida = $valida;

        return $this;
    }

    public function getEsMarcadoComoTerminalDues(): ?bool
    {
        return $this->es_marcado_como_terminal_dues;
    }

    public function setEsMarcadoComoTerminalDues(?bool $es_marcado_como_terminal_dues): self
    {
        $this->es_marcado_como_terminal_dues = $es_marcado_como_terminal_dues;

        return $this;
    }

    public function getEsPrimeroCalcularHTD(): ?bool
    {
        return $this->es_primero_calcular_HTD;
    }

    public function setEsPrimeroCalcularHTD(?bool $es_primero_calcular_HTD): self
    {
        $this->es_primero_calcular_HTD = $es_primero_calcular_HTD;

        return $this;
    }

    public function getCodigoLecturaBorrada(): ?string
    {
        return $this->codigo_lectura_borrada;
    }

    public function setCodigoLecturaBorrada(?string $codigo_lectura_borrada): self
    {
        $this->codigo_lectura_borrada = $codigo_lectura_borrada;

        return $this;
    }

    public function getDetalleLecturaBorrada(): ?string
    {
        return $this->detalle_lectura_borrada;
    }

    public function setDetalleLecturaBorrada(?string $detalle_lectura_borrada): self
    {
        $this->detalle_lectura_borrada = $detalle_lectura_borrada;

        return $this;
    }

    public function getCtdLecturasLuegoEntregado(): ?int
    {
        return $this->ctd_lecturas_luego_entregado;
    }

    public function setCtdLecturasLuegoEntregado(int $ctd_lecturas_luego_entregado): self
    {
        $this->ctd_lecturas_luego_entregado = $ctd_lecturas_luego_entregado;

        return $this;
    }

    public function getTieneLecturasMarcadasComoTD(): ?bool
    {
        return $this->tiene_lecturas_marcadas_como_TD;
    }

    public function setTieneLecturasMarcadasComoTD(?bool $tiene_lecturas_marcadas_como_TD): self
    {
        $this->tiene_lecturas_marcadas_como_TD = $tiene_lecturas_marcadas_como_TD;

        return $this;
    }

    public function getCantLecturasEntreEnviadoYRecibido(): ?int
    {
        return $this->cant_lecturas_entre_enviado_y_recibido;
    }

    public function setCantLecturasEntreEnviadoYRecibido(int $cant_lecturas_entre_enviado_y_recibido): self
    {
        $this->cant_lecturas_entre_enviado_y_recibido = $cant_lecturas_entre_enviado_y_recibido;

        return $this;
    }

    public function getCantLecturasDespuesRecibido(): ?int
    {
        return $this->cant_lecturas_despues_recibido;
    }

    public function setCantLecturasDespuesRecibido(int $cant_lecturas_despues_recibido): self
    {
        $this->cant_lecturas_despues_recibido = $cant_lecturas_despues_recibido;

        return $this;
    }

    public function getLector(): ?Lector
    {
        return $this->lector;
    }

    public function setLector(?Lector $lector): self
    {
        $this->lector = $lector;

        return $this;
    }

    public function getEnvio(): ?Envio
    {
        return $this->envio;
    }

    public function setEnvio(?Envio $envio): self
    {
        $this->envio = $envio;

        return $this;
    }
}

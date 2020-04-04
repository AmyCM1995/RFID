<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LecturasCsvRepository")
 */
class LecturasCsv
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $codigo_pais_origen;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $codigo_ciudad_origen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_ciudad_origen;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $codigo_area_origen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_area_origen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipo_dimension;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $id_envio;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $id_transpondedor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fecha_plan_enviada;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fecha_real_enviada;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $codigo_pais_destino;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $codigo_ciudad_destino;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_ciudad_destino;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $codigo_area_destino;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_area_destino;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fecha_recibida;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $validado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $valido;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hora_fecha_lectura;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dia_lectura;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $codigo_sitio_pais;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $codigo_sitio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_sitio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_sitio_area;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_lector;

    /**
     * @ORM\Column(type="string", length=18, nullable=true)
     */
    private $id_lector;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $proposito_lector;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ctd_lecturas_luego_entregado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $tiene_lecturas_marcadas_como_TD;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cant_lecturas_entre_enviado_y_recibido;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cant_lecturas_despues_recibido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BorradoPropio", inversedBy="lecturasCsvs")
     */
    private $codigo_borrado_propio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ImportacionesLecturas", inversedBy="lecturasCsvs")
     */
    private $importacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigoPaisOrigen(): ?string
    {
        return $this->codigo_pais_origen;
    }

    public function setCodigoPaisOrigen(?string $codigo_pais_origen): self
    {
        $this->codigo_pais_origen = $codigo_pais_origen;

        return $this;
    }

    public function getCodigoCiudadOrigen(): ?string
    {
        return $this->codigo_ciudad_origen;
    }

    public function setCodigoCiudadOrigen(?string $codigo_ciudad_origen): self
    {
        $this->codigo_ciudad_origen = $codigo_ciudad_origen;

        return $this;
    }

    public function getNombreCiudadOrigen(): ?string
    {
        return $this->nombre_ciudad_origen;
    }

    public function setNombreCiudadOrigen(?string $nombre_ciudad_origen): self
    {
        $this->nombre_ciudad_origen = $nombre_ciudad_origen;

        return $this;
    }

    public function getCodigoAreaOrigen(): ?string
    {
        return $this->codigo_area_origen;
    }

    public function setCodigoAreaOrigen(?string $codigo_area_origen): self
    {
        $this->codigo_area_origen = $codigo_area_origen;

        return $this;
    }

    public function getNombreAreaOrigen(): ?string
    {
        return $this->nombre_area_origen;
    }

    public function setNombreAreaOrigen(?string $nombre_area_origen): self
    {
        $this->nombre_area_origen = $nombre_area_origen;

        return $this;
    }

    public function getTipoDimension(): ?string
    {
        return $this->tipo_dimension;
    }

    public function setTipoDimension(?string $tipo_dimension): self
    {
        $this->tipo_dimension = $tipo_dimension;

        return $this;
    }

    public function getIdEnvio(): ?string
    {
        return $this->id_envio;
    }

    public function setIdEnvio(?string $id_envio): self
    {
        $this->id_envio = $id_envio;

        return $this;
    }

    public function getIdTranspondedor(): ?string
    {
        return $this->id_transpondedor;
    }

    public function setIdTranspondedor(?string $id_transpondedor): self
    {
        $this->id_transpondedor = $id_transpondedor;

        return $this;
    }

    public function getFechaPlanEnviada(): ?string
    {
        return $this->fecha_plan_enviada;
    }

    public function setFechaPlanEnviada(?string $fecha_plan_enviada): self
    {
        $this->fecha_plan_enviada = $fecha_plan_enviada;

        return $this;
    }

    public function getFechaRealEnviada(): ?string
    {
        return $this->fecha_real_enviada;
    }

    public function setFechaRealEnviada(?string $fecha_real_enviada): self
    {
        $this->fecha_real_enviada = $fecha_real_enviada;

        return $this;
    }

    public function getCodigoPaisDestino(): ?string
    {
        return $this->codigo_pais_destino;
    }

    public function setCodigoPaisDestino(?string $codigo_pais_destino): self
    {
        $this->codigo_pais_destino = $codigo_pais_destino;

        return $this;
    }

    public function getCodigoCiudadDestino(): ?string
    {
        return $this->codigo_ciudad_destino;
    }

    public function setCodigoCiudadDestino(?string $codigo_ciudad_destino): self
    {
        $this->codigo_ciudad_destino = $codigo_ciudad_destino;

        return $this;
    }

    public function getNombreCiudadDestino(): ?string
    {
        return $this->nombre_ciudad_destino;
    }

    public function setNombreCiudadDestino(?string $nombre_ciudad_destino): self
    {
        $this->nombre_ciudad_destino = $nombre_ciudad_destino;

        return $this;
    }

    public function getCodigoAreaDestino(): ?string
    {
        return $this->codigo_area_destino;
    }

    public function setCodigoAreaDestino(?string $codigo_area_destino): self
    {
        $this->codigo_area_destino = $codigo_area_destino;

        return $this;
    }

    public function getNombreAreaDestino(): ?string
    {
        return $this->nombre_area_destino;
    }

    public function setNombreAreaDestino(?string $nombre_area_destino): self
    {
        $this->nombre_area_destino = $nombre_area_destino;

        return $this;
    }

    public function getFechaRecibida(): ?string
    {
        return $this->fecha_recibida;
    }

    public function setFechaRecibida(?string $fecha_recibida): self
    {
        $this->fecha_recibida = $fecha_recibida;

        return $this;
    }

    public function getValidado(): ?bool
    {
        return $this->validado;
    }

    public function setValidado(?bool $validado): self
    {
        $this->validado = $validado;

        return $this;
    }

    public function getValido(): ?bool
    {
        return $this->valido;
    }

    public function setValido(?bool $valido): self
    {
        $this->valido = $valido;

        return $this;
    }

    public function getHoraFechaLectura(): ?string
    {
        return $this->hora_fecha_lectura;
    }

    public function setHoraFechaLectura(?string $hora_fecha_lectura): self
    {
        $this->hora_fecha_lectura = $hora_fecha_lectura;

        return $this;
    }

    public function getDiaLectura(): ?string
    {
        return $this->dia_lectura;
    }

    public function setDiaLectura(?string $dia_lectura): self
    {
        $this->dia_lectura = $dia_lectura;

        return $this;
    }

    public function getCodigoSitioPais(): ?string
    {
        return $this->codigo_sitio_pais;
    }

    public function setCodigoSitioPais(?string $codigo_sitio_pais): self
    {
        $this->codigo_sitio_pais = $codigo_sitio_pais;

        return $this;
    }

    public function getCodigoSitio(): ?string
    {
        return $this->codigo_sitio;
    }

    public function setCodigoSitio(?string $codigo_sitio): self
    {
        $this->codigo_sitio = $codigo_sitio;

        return $this;
    }

    public function getNombreSitio(): ?string
    {
        return $this->nombre_sitio;
    }

    public function setNombreSitio(?string $nombre_sitio): self
    {
        $this->nombre_sitio = $nombre_sitio;

        return $this;
    }

    public function getNombreSitioArea(): ?string
    {
        return $this->nombre_sitio_area;
    }

    public function setNombreSitioArea(?string $nombre_sitio_area): self
    {
        $this->nombre_sitio_area = $nombre_sitio_area;

        return $this;
    }

    public function getNombreLector(): ?string
    {
        return $this->nombre_lector;
    }

    public function setNombreLector(?string $nombre_lector): self
    {
        $this->nombre_lector = $nombre_lector;

        return $this;
    }

    public function getIdLector(): ?string
    {
        return $this->id_lector;
    }

    public function setIdLector(?string $id_lector): self
    {
        $this->id_lector = $id_lector;

        return $this;
    }

    public function getPropositoLector(): ?string
    {
        return $this->proposito_lector;
    }

    public function setPropositoLector(?string $proposito_lector): self
    {
        $this->proposito_lector = $proposito_lector;

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

    public function getCtdLecturasLuegoEntregado(): ?string
    {
        return $this->ctd_lecturas_luego_entregado;
    }

    public function setCtdLecturasLuegoEntregado(?string $ctd_lecturas_luego_entregado): self
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

    public function setCantLecturasEntreEnviadoYRecibido(?int $cant_lecturas_entre_enviado_y_recibido): self
    {
        $this->cant_lecturas_entre_enviado_y_recibido = $cant_lecturas_entre_enviado_y_recibido;

        return $this;
    }

    public function getCantLecturasDespuesRecibido(): ?int
    {
        return $this->cant_lecturas_despues_recibido;
    }

    public function setCantLecturasDespuesRecibido(?int $cant_lecturas_despues_recibido): self
    {
        $this->cant_lecturas_despues_recibido = $cant_lecturas_despues_recibido;

        return $this;
    }

    public function getCodigoBorradoPropio(): ?BorradoPropio
    {
        return $this->codigo_borrado_propio;
    }

    public function setCodigoBorradoPropio(?BorradoPropio $codigo_borrado_propio): self
    {
        $this->codigo_borrado_propio = $codigo_borrado_propio;

        return $this;
    }

    public function getImportacion(): ?ImportacionesLecturas
    {
        return $this->importacion;
    }

    public function setImportacion(?ImportacionesLecturas $importacion): self
    {
        $this->importacion = $importacion;

        return $this;
    }
}

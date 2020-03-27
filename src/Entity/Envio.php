<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EnvioRepository")
 */
class Envio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $codigo_transpondedor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo_medida;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha_plan_enviado;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha_real_enviado;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lectura", mappedBy="envio")
     */
    private $lecturas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Area", inversedBy="envios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area_origen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Area", inversedBy="envios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area_destino;

    public function __construct()
    {
        $this->lecturas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getCodigoTranspondedor(): ?string
    {
        return $this->codigo_transpondedor;
    }

    public function setCodigoTranspondedor(string $codigo_transpondedor): self
    {
        $this->codigo_transpondedor = $codigo_transpondedor;

        return $this;
    }

    public function getTipoMedida(): ?string
    {
        return $this->tipo_medida;
    }

    public function setTipoMedida(string $tipo_medida): self
    {
        $this->tipo_medida = $tipo_medida;

        return $this;
    }

    public function getFechaPlanEnviado(): ?\DateTimeInterface
    {
        return $this->fecha_plan_enviado;
    }

    public function setFechaPlanEnviado(\DateTimeInterface $fecha_plan_enviado): self
    {
        $this->fecha_plan_enviado = $fecha_plan_enviado;

        return $this;
    }

    public function getFechaRealEnviado(): ?\DateTimeInterface
    {
        return $this->fecha_real_enviado;
    }

    public function setFechaRealEnviado(\DateTimeInterface $fecha_real_enviado): self
    {
        $this->fecha_real_enviado = $fecha_real_enviado;

        return $this;
    }

    /**
     * @return Collection|Lectura[]
     */
    public function getLecturas(): Collection
    {
        return $this->lecturas;
    }

    public function addLectura(Lectura $lectura): self
    {
        if (!$this->lecturas->contains($lectura)) {
            $this->lecturas[] = $lectura;
            $lectura->setEnvio($this);
        }

        return $this;
    }

    public function removeLectura(Lectura $lectura): self
    {
        if ($this->lecturas->contains($lectura)) {
            $this->lecturas->removeElement($lectura);
            // set the owning side to null (unless already changed)
            if ($lectura->getEnvio() === $this) {
                $lectura->setEnvio(null);
            }
        }

        return $this;
    }

    public function getAreaOrigen(): ?Area
    {
        return $this->area_origen;
    }

    public function setAreaOrigen(?Area $area_origen): self
    {
        $this->area_origen = $area_origen;

        return $this;
    }

    public function getAreaDestino(): ?Area
    {
        return $this->area_destino;
    }

    public function setAreaDestino(?Area $area_destino): self
    {
        $this->area_destino = $area_destino;

        return $this;
    }

}

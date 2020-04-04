<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BorradoPropioRepository")
 */
class BorradoPropio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $detalle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LecturasCsv", mappedBy="codigo_borrado_propio")
     */
    private $lecturasCsvs;

    /**
     * @ORM\Column(type="boolean")
     */
    private $es_editable;

    public function __construct()
    {
        $this->lecturasCsvs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDetalle(): ?string
    {
        return $this->detalle;
    }

    public function setDetalle(string $detalle): self
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * @return Collection|LecturasCsv[]
     */
    public function getLecturasCsvs(): Collection
    {
        return $this->lecturasCsvs;
    }

    public function addLecturasCsv(LecturasCsv $lecturasCsv): self
    {
        if (!$this->lecturasCsvs->contains($lecturasCsv)) {
            $this->lecturasCsvs[] = $lecturasCsv;
            $lecturasCsv->setCodigoBorradoPropio($this);
        }

        return $this;
    }

    public function removeLecturasCsv(LecturasCsv $lecturasCsv): self
    {
        if ($this->lecturasCsvs->contains($lecturasCsv)) {
            $this->lecturasCsvs->removeElement($lecturasCsv);
            // set the owning side to null (unless already changed)
            if ($lecturasCsv->getCodigoBorradoPropio() === $this) {
                $lecturasCsv->setCodigoBorradoPropio(null);
            }
        }

        return $this;
    }

    public function getEsEditable(): ?bool
    {
        return $this->es_editable;
    }

    public function setEsEditable(bool $es_editable): self
    {
        $this->es_editable = $es_editable;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImportacionesLecturasRepository")
 */
class ImportacionesLecturas
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
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LecturasCsv", mappedBy="importacion")
     */
    private $lecturasCsvs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lecturas", mappedBy="importacion")
     */
    private $lecturas;

    public function __construct()
    {
        $this->lecturasCsvs = new ArrayCollection();
        $this->lecturas = new ArrayCollection();
        $this->incidencias = new ArrayCollection();
    }

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
            $lecturasCsv->setImportacion($this);
        }

        return $this;
    }

    public function removeLecturasCsv(LecturasCsv $lecturasCsv): self
    {
        if ($this->lecturasCsvs->contains($lecturasCsv)) {
            $this->lecturasCsvs->removeElement($lecturasCsv);
            // set the owning side to null (unless already changed)
            if ($lecturasCsv->getImportacion() === $this) {
                $lecturasCsv->setImportacion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Lecturas[]
     */
    public function getLecturas(): Collection
    {
        return $this->lecturas;
    }

    public function addLectura(Lecturas $lectura): self
    {
        if (!$this->lecturas->contains($lectura)) {
            $this->lecturas[] = $lectura;
            $lectura->setImportacion($this);
        }

        return $this;
    }

    public function removeLectura(Lecturas $lectura): self
    {
        if ($this->lecturas->contains($lectura)) {
            $this->lecturas->removeElement($lectura);
            // set the owning side to null (unless already changed)
            if ($lectura->getImportacion() === $this) {
                $lectura->setImportacion(null);
            }
        }

        return $this;
    }


}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IPLectorCubanoRepository")
 */
class IPLectorCubano
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Lector", inversedBy="iPLectorCubano", cascade={"persist", "remove"})
     */
    private $lector;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HistorialLectores", mappedBy="ipLector")
     */
    private $historialLectores;

    public function __construct()
    {
        $this->historialLectores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return Collection|HistorialLectores[]
     */
    public function getHistorialLectores(): Collection
    {
        return $this->historialLectores;
    }

    public function addHistorialLectore(HistorialLectores $historialLectore): self
    {
        if (!$this->historialLectores->contains($historialLectore)) {
            $this->historialLectores[] = $historialLectore;
            $historialLectore->setIpLector($this);
        }

        return $this;
    }

    public function removeHistorialLectore(HistorialLectores $historialLectore): self
    {
        if ($this->historialLectores->contains($historialLectore)) {
            $this->historialLectores->removeElement($historialLectore);
            // set the owning side to null (unless already changed)
            if ($historialLectore->getIpLector() === $this) {
                $historialLectore->setIpLector(null);
            }
        }

        return $this;
    }
}

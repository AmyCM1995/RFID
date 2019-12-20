<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RolRepository")
 */
class Rol
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
    private $nombreRol;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GMSRFIDUsuario", mappedBy="rolUsuario", orphanRemoval=true)
     */
    private $gMSRFIDUsuarios;

    public function __construct()
    {
        $this->gMSRFIDUsuarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreRol(): ?string
    {
        return $this->nombreRol;
    }

    public function setNombreRol(string $nombreRol): self
    {
        $this->nombreRol = $nombreRol;

        return $this;
    }

    /**
     * @return Collection|GMSRFIDUsuario[]
     */
    public function getGMSRFIDUsuarios(): Collection
    {
        return $this->gMSRFIDUsuarios;
    }

    public function addGMSRFIDUsuario(GMSRFIDUsuario $gMSRFIDUsuario): self
    {
        if (!$this->gMSRFIDUsuarios->contains($gMSRFIDUsuario)) {
            $this->gMSRFIDUsuarios[] = $gMSRFIDUsuario;
            $gMSRFIDUsuario->setRolUsuario($this);
        }

        return $this;
    }

    public function removeGMSRFIDUsuario(GMSRFIDUsuario $gMSRFIDUsuario): self
    {
        if ($this->gMSRFIDUsuarios->contains($gMSRFIDUsuario)) {
            $this->gMSRFIDUsuarios->removeElement($gMSRFIDUsuario);
            // set the owning side to null (unless already changed)
            if ($gMSRFIDUsuario->getRolUsuario() === $this) {
                $gMSRFIDUsuario->setRolUsuario(null);
            }
        }

        return $this;
    }
}

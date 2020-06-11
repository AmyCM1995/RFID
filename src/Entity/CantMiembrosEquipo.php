<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CantMiembrosEquipoRepository")
 */
class CantMiembrosEquipo
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
    private $cantidad;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EquipoCorresponsales", mappedBy="cant_miembros")
     */
    private $equipoCOrresponsales;

    public function __construct()
    {
        $this->equipoCOrresponsales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * @return Collection|EquipoCorresponsales[]
     */
    public function getEquipoCOrresponsales(): Collection
    {
        return $this->equipoCOrresponsales;
    }

    public function addEquipoCOrresponsale(EquipoCorresponsales $equipoCOrresponsale): self
    {
        if (!$this->equipoCOrresponsales->contains($equipoCOrresponsale)) {
            $this->equipoCOrresponsales[] = $equipoCOrresponsale;
            $equipoCOrresponsale->setCantMiembros($this);
        }

        return $this;
    }

    public function removeEquipoCOrresponsale(EquipoCorresponsales $equipoCOrresponsale): self
    {
        if ($this->equipoCOrresponsales->contains($equipoCOrresponsale)) {
            $this->equipoCOrresponsales->removeElement($equipoCOrresponsale);
            // set the owning side to null (unless already changed)
            if ($equipoCOrresponsale->getCantMiembros() === $this) {
                $equipoCOrresponsale->setCantMiembros(null);
            }
        }

        return $this;
    }
}

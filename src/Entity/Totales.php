<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TotalesRepository")
 */
class Totales
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $corresponsalCuba;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $corresponsalDestino;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalEnvios;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCorresponsalCuba(): ?string
    {
        return $this->corresponsalCuba;
    }

    public function setCorresponsalCuba(?string $corresponsalCuba): self
    {
        $this->corresponsalCuba = $corresponsalCuba;

        return $this;
    }

    public function getCorresponsalDestino(): ?string
    {
        return $this->corresponsalDestino;
    }

    public function setCorresponsalDestino(?string $corresponsalDestino): self
    {
        $this->corresponsalDestino = $corresponsalDestino;

        return $this;
    }

    public function getTotalEnvios(): ?int
    {
        return $this->totalEnvios;
    }

    public function setTotalEnvios(int $totalEnvios): self
    {
        $this->totalEnvios = $totalEnvios;

        return $this;
    }
}

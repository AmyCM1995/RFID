<?php

namespace App\Entity;

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
}

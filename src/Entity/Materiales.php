<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MaterialesRepository")
 */
class Materiales
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
    private $identificador;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Corresponsal", inversedBy="materiales")
     */
    private $corresponsal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importaciones", inversedBy="materiales")
     */
    private $importacionPi;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentificador(): ?string
    {
        return $this->identificador;
    }

    public function setIdentificador(string $identificador): self
    {
        $this->identificador = $identificador;

        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getCorresponsal(): ?Corresponsal
    {
        return $this->corresponsal;
    }

    public function setCorresponsal(?Corresponsal $corresponsal): self
    {
        $this->corresponsal = $corresponsal;

        return $this;
    }

    public function getImportacionPi(): ?Importaciones
    {
        return $this->importacionPi;
    }

    public function setImportacionPi(?Importaciones $importacionPi): self
    {
        $this->importacionPi = $importacionPi;

        return $this;
    }
}

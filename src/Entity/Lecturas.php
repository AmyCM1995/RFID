<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LecturasRepository")
 */
class Lecturas
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ImportacionesLecturas", inversedBy="lecturas")
     */
    private $importacion;

    public function getId(): ?int
    {
        return $this->id;
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

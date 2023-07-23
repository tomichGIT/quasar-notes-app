<?php

namespace App\Entity;

use App\Repository\CategoriasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriasRepository::class)]
class Categorias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $categoria = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToMany(targetEntity: Notas::class, inversedBy: 'categorias')]
    private Collection $notas;

    public function __construct()
    {
        $this->notas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Notas>
     */
    public function getNotas(): Collection
    {
        return $this->notas;
    }

    public function addNota(Notas $nota): static
    {
        if (!$this->notas->contains($nota)) {
            $this->notas->add($nota);
        }

        return $this;
    }

    public function removeNota(Notas $nota): static
    {
        $this->notas->removeElement($nota);

        return $this;
    }
}

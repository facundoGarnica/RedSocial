<?php

namespace App\Entity;

use App\Repository\ComentarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComentarioRepository::class)]
class Comentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

    #[ORM\ManyToOne(inversedBy: 'comentarios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\OneToMany(targetEntity: ReaccionComentario::class, mappedBy: 'comentario', orphanRemoval: true)]
    private Collection $reaccioncomentarios;

    #[ORM\ManyToOne(inversedBy: 'comentarios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    public function __construct()
    {
        $this->reaccioncomentarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, ReaccionComentario>
     */
    public function getReaccioncomentarios(): Collection
    {
        return $this->reaccioncomentarios;
    }

    public function addReaccioncomentario(ReaccionComentario $reaccioncomentario): static
    {
        if (!$this->reaccioncomentarios->contains($reaccioncomentario)) {
            $this->reaccioncomentarios->add($reaccioncomentario);
            $reaccioncomentario->setComentario($this);
        }

        return $this;
    }

    public function removeReaccioncomentario(ReaccionComentario $reaccioncomentario): static
    {
        if ($this->reaccioncomentarios->removeElement($reaccioncomentario)) {
            // set the owning side to null (unless already changed)
            if ($reaccioncomentario->getComentario() === $this) {
                $reaccioncomentario->setComentario(null);
            }
        }

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }
}

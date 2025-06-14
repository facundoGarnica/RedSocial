<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Descripcion = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $FechaCreacion = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\OneToMany(targetEntity: Comentario::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $comentarios;

    #[ORM\OneToMany(targetEntity: ReaccionPost::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $reaccionposts;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->reaccionposts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->Descripcion;
    }

    public function setDescripcion(?string $Descripcion): static
    {
        $this->Descripcion = $Descripcion;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->FechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $FechaCreacion): static
    {
        $this->FechaCreacion = $FechaCreacion;

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
     * @return Collection<int, Comentario>
     */
    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function addComentario(Comentario $comentario): static
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios->add($comentario);
            $comentario->setPost($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): static
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getPost() === $this) {
                $comentario->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReaccionPost>
     */
    public function getReaccionposts(): Collection
    {
        return $this->reaccionposts;
    }

    public function addReaccionpost(ReaccionPost $reaccionpost): static
    {
        if (!$this->reaccionposts->contains($reaccionpost)) {
            $this->reaccionposts->add($reaccionpost);
            $reaccionpost->setPost($this);
        }

        return $this;
    }

    public function removeReaccionpost(ReaccionPost $reaccionpost): static
    {
        if ($this->reaccionposts->removeElement($reaccionpost)) {
            // set the owning side to null (unless already changed)
            if ($reaccionpost->getPost() === $this) {
                $reaccionpost->setPost(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->titulo ?? 'Sin título';
    }
}

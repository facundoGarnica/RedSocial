<?php

namespace App\Entity;

use App\Repository\ReaccionComentarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReaccionComentarioRepository::class)]
class ReaccionComentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $emoticon = null;

    #[ORM\ManyToOne(inversedBy: 'reaccioncomentarios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'reaccioncomentarios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comentario $comentario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmoticon(): ?string
    {
        return $this->emoticon;
    }

    public function setEmoticon(string $emoticon): static
    {
        $this->emoticon = $emoticon;

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

    public function getComentario(): ?Comentario
    {
        return $this->comentario;
    }

    public function setComentario(?Comentario $comentario): static
    {
        $this->comentario = $comentario;

        return $this;
    }
}

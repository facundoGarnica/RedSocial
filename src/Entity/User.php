<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'Users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Persona $persona = null;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'usuario', orphanRemoval: true)]
    private Collection $posts;

    #[ORM\OneToMany(targetEntity: ReaccionPost::class, mappedBy: 'usuario', orphanRemoval: true)]
    private Collection $reaccionposts;

    #[ORM\OneToMany(targetEntity: ReaccionComentario::class, mappedBy: 'usuario', orphanRemoval: true)]
    private Collection $reaccioncomentarios;

    #[ORM\OneToMany(targetEntity: Comentario::class, mappedBy: 'usuario', orphanRemoval: true)]
    private Collection $comentarios;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->reaccionposts = new ArrayCollection();
        $this->reaccioncomentarios = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): static
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUsuario($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUsuario() === $this) {
                $post->setUsuario(null);
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
            $reaccionpost->setUsuario($this);
        }

        return $this;
    }

    public function removeReaccionpost(ReaccionPost $reaccionpost): static
    {
        if ($this->reaccionposts->removeElement($reaccionpost)) {
            // set the owning side to null (unless already changed)
            if ($reaccionpost->getUsuario() === $this) {
                $reaccionpost->setUsuario(null);
            }
        }

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
            $reaccioncomentario->setUsuario($this);
        }

        return $this;
    }

    public function removeReaccioncomentario(ReaccionComentario $reaccioncomentario): static
    {
        if ($this->reaccioncomentarios->removeElement($reaccioncomentario)) {
            // set the owning side to null (unless already changed)
            if ($reaccioncomentario->getUsuario() === $this) {
                $reaccioncomentario->setUsuario(null);
            }
        }

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
            $comentario->setUsuario($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): static
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getUsuario() === $this) {
                $comentario->setUsuario(null);
            }
        }

        return $this;
    }
}

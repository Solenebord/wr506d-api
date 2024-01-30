<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['created' => 'DESC'])]
#[ORM\HasLifecycleCallbacks]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_updated = null;

    #[ORM\OneToMany(mappedBy: 'fk_category', targetEntity: Movie::class)]
    private Collection $fk_movies;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'fk_categories')]
    private Collection $fk_users;

    public function __construct()
    {
        $this->fk_movies = new ArrayCollection();
        $this->fk_users = new ArrayCollection();
        $this->created = new \DateTime();
        $this->last_updated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->last_updated;
    }

    public function setLastUpdated(\DateTimeInterface $last_updated): self
    {
        $this->last_updated = $last_updated;

        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->setLastUpdated(new \DateTime());
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getFkMovies(): Collection
    {
        return $this->fk_movies;
    }

    public function addFkMovie(Movie $fkMovie): static
    {
        if (!$this->fk_movies->contains($fkMovie)) {
            $this->fk_movies->add($fkMovie);
            $fkMovie->setFkCategory($this);
        }

        return $this;
    }

    public function removeFkMovie(Movie $fkMovie): static
    {
        if ($this->fk_movies->removeElement($fkMovie)) {
            // set the owning side to null (unless already changed)
            if ($fkMovie->getFkCategory() === $this) {
                $fkMovie->setFkCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFkUsers(): Collection
    {
        return $this->fk_users;
    }

    public function addFkUser(User $fkUser): static
    {
        if (!$this->fk_users->contains($fkUser)) {
            $this->fk_users->add($fkUser);
            $fkUser->addFkCategory($this);
        }

        return $this;
    }

    public function removeFkUser(User $fkUser): static
    {
        if ($this->fk_users->removeElement($fkUser)) {
            $fkUser->removeFkCategory($this);
        }

        return $this;
    }
}

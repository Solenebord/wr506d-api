<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[ApiResource(types: ['https://schema.org/Book'])]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['created' => 'DESC'])]
#[ORM\HasLifecycleCallbacks]
class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $birth = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biography = null;

    #[ORM\ManyToOne(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[ApiProperty(types: ['https://schema.org/image'])]
    public ?MediaObject $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_updated = null;

    #[ORM\ManyToMany(targetEntity: Movie::class, mappedBy: 'fk_actor')]
    private Collection $fk_movies;

    public function __construct()
    {
        $this->fk_movies = new ArrayCollection();
        $this->created = new \DateTime();
        $this->last_updated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBirth(): ?string
    {
        return $this->birth;
    }

    public function setBirth(string $birth): static
    {
        $this->birth = $birth;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): static
    {
        $this->biography = $biography;

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
            $fkMovie->addFkActor($this);
        }

        return $this;
    }

    public function removeFkMovie(Movie $fkMovie): static
    {
        if ($this->fk_movies->removeElement($fkMovie)) {
            $fkMovie->removeFkActor($this);
        }

        return $this;
    }

    public function setImage(?MediaObject $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->image->getFilepath();
    }
}

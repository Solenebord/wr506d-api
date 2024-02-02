<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiResource(types: ['https://schema.org/Book'])]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['created' => 'DESC'])]
#[ORM\HasLifecycleCallbacks]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $released = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resume = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column(length: 255)]
    private ?string $director = null;

    #[ORM\ManyToOne(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[ApiProperty(types: ['https://schema.org/image'])]
    public ?MediaObject $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_updated = null;

    #[ORM\ManyToMany(targetEntity: Actor::class, inversedBy: 'fk_movies')]
    private Collection $fk_actor;

    #[ORM\ManyToOne(inversedBy: 'fk_movies')]
    private ?Category $fk_category = null;

    public function __construct()
    {
        $this->fk_actor = new ArrayCollection();
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

    public function getReleased(): ?string
    {
        return $this->released;
    }

    public function setReleased(string $released): static
    {
        $this->released = $released;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(string $director): static
    {
        $this->director = $director;

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
     * @return Collection<int, Actor>
     */
    public function getFkActor(): Collection
    {
        return $this->fk_actor;
    }

    public function addFkActor(Actor $fkActor): static
    {
        if (!$this->fk_actor->contains($fkActor)) {
            $this->fk_actor->add($fkActor);
        }

        return $this;
    }

    public function removeFkActor(Actor $fkActor): static
    {
        $this->fk_actor->removeElement($fkActor);

        return $this;
    }

    public function getFkCategory(): ?Category
    {
        return $this->fk_category;
    }

    public function setFkCategory(?Category $fk_category): static
    {
        $this->fk_category = $fk_category;

        return $this;
    }

    public function setImage(?MediaObject $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->image ? $this->image->getFilepath() : '';
    }
}

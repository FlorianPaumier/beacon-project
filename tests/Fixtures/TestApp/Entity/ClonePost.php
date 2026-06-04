<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'clone_post')]
class ClonePost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $name = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $slug = '';

    #[ORM\ManyToOne(targetEntity: CloneCategory::class, inversedBy: 'posts')]
    private ?CloneCategory $category = null;

    #[ORM\ManyToMany(targetEntity: CloneTag::class)]
    /** @var Collection<int, CloneTag> */
    private Collection $tags;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?CloneCategory
    {
        return $this->category;
    }

    public function setCategory(?CloneCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /** @return Collection<int, CloneTag> */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(CloneTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}

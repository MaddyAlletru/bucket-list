<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Wish::class)]
    private Collection $wishesList;

    public function __construct()
    {
        $this->wishesList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /** * @return Collection<int, Wish>
     */
    public function getWishesList(): Collection
    {
        return $this->wishesList;
    }

    public function addWishesList(Wish $wishesList): self
    {
        if (!$this->wishesList->contains($wishesList)) {
            $this->wishesList->add($wishesList);
            $wishesList->setCategory($this);
        }
        return $this;
    }

    public function removeWishesList(Wish $wishesList): self
    {
        if ($this->wishesList->removeElement($wishesList)) {
            // set the owning side to null (unless already changed)
            if ($wishesList->getCategory() === $this) {
                $wishesList->setCategory(null);
            }
        }
        return $this;
    }


}

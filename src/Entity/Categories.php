<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "categories")]
#[ORM\Entity]
class Categories
{
    #[ORM\Column(name: "categories_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $categoriesId;

    #[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Name is required")]
    private $name;

    public function getCategoriesId(): ?int
    {
        return $this->categoriesId;
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

    public function __toString(): string
        {
            return $this->getName();
        }
}

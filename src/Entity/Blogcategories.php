<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "blogcategories")]
class Blogcategories
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "categories_ID", type: "integer")]
    private $categoriesId;

    #[ORM\Column(name: "name", type: "string", length: 255)]
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


}

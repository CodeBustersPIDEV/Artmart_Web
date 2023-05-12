<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "tags")]
#[ORM\Entity]
class Tags
{

    #[ORM\Column(name: "tags_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $tagsId;

    #[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Please Enter A Name For The Tag")]

    private $name;

    public function getTagsId(): ?int
    {
        return $this->tagsId;
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

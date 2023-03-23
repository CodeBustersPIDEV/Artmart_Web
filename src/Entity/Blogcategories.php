<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blogcategories
 *
 * @ORM\Table(name="blogcategories")
 * @ORM\Entity
 */
class Blogcategories
{
    /**
     * @var int
     *
     * @ORM\Column(name="categories_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $categoriesId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
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

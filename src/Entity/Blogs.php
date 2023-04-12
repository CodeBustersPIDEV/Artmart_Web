<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\BlogsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogsRepository::class)]
#[ORM\Table(name: "blogs")]
class Blogs
{
    #[ORM\Id]
    #[ORM\Column(name: "blogs_ID", type: "integer", nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $blogs_ID;

    #[ORM\Column(name: "title", type: "string", length: 255, nullable: false)]
    private $title;

    #[ORM\Column(name: "content", type: "text", length: 65535, nullable: false)]
    private $content;

    #[ORM\Column(name: "date", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"])]
    private $date;

    #[ORM\Column(name: "rating", type: "float", precision: 10, scale: 0, nullable: true, options: ["default" => "NULL"])]
    private $rating;

    #[ORM\Column(name: "nb_views", type: "integer", nullable: false)]
    private $nbViews;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "author", referencedColumnName: "user_ID")]
    private $author;

    public function getBlogsId(): ?int
    {
        return $this->blogs_ID;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getNbViews(): ?int
    {
        return $this->nbViews;
    }

    public function setNbViews(int $nbViews): self
    {
        $this->nbViews = $nbViews;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author ?? null;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "comments", options: [
    'indexes' => [
        'author' => ['columns' => ['author']],
        'tag_id' => ['columns' => ['blog_ID']]
    ]
])]
#[ORM\Entity]
class Comments
{
    #[ORM\Column(name: "comments_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $commentsId;

    #[ORM\Column(name: "content", type: "text", length: 65535, nullable: false)]
    private $content;

    #[ORM\Column(name: "date", type: "datetime", nullable: true, options: ["default" => "current_timestamp()"])]
    private $date;

    #[ORM\Column(name: "rating", type: "integer", nullable: true, options: ["default" => "NULL"])]
    private $rating = NULL;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "author", referencedColumnName: "user_ID")]
    private $author;

    #[ORM\ManyToOne(targetEntity: "Blogs")]
    #[ORM\JoinColumn(name: "blog_ID", referencedColumnName: "blogs_ID")]
    private $blog_ID;

    public function getCommentsId(): ?int
    {
        return $this->commentsId;
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

    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

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

    public function getBlog(): ?Blogs
    {
        return $this->blog_ID ?? null;
    }

    public function setBlog(?Blogs $blog): self
    {
        $this->blog_ID = $blog;

        return $this;
    }
}

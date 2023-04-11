<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "blog_tags", options: [
    'indexes' => [
        'blog_id' => ['columns' => ['blog_id']],
        'tag_id' => ['columns' => ['tag_id']]
    ]
])]
#[ORM\Entity]
class BlogTags
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\ManyToOne(targetEntity: "Blogs")]
    #[ORM\JoinColumn(name: "blog_id", referencedColumnName: "blogs_ID")]
    private $blog_id;

    #[ORM\ManyToOne(targetEntity: "Tags")]
    #[ORM\JoinColumn(name: "tag_id", referencedColumnName: "tags_ID")]
    private $tag_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlog(): ?Blogs
    {
        return $this->blog_id ?? null;
    }

    public function setBlog(?Blogs $blog): self
    {
        $this->blog_id = $blog;

        return $this;
    }

    public function getTag(): ?Tags
    {
        return $this->tag_id ?? null;
    }

    public function setTag(?Tags $tag): self
    {
        $this->tag_id = $tag;

        return $this;
    }
}

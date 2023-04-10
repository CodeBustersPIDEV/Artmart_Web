<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlogTags
 *
 * @ORM\Table(name="blog_tags", indexes={@ORM\Index(name="blog_id", columns={"blog_id"}), @ORM\Index(name="tag_id", columns={"tag_id"})})
 * @ORM\Entity
 */
class BlogTags
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Blogs
     *
     * @ORM\ManyToOne(targetEntity="Blogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="blog_id", referencedColumnName="blogs_ID")
     * })
     */
    private $blog_id;

    /**
     * @var \Tags
     *
     * @ORM\ManyToOne(targetEntity="Tags")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tag_id", referencedColumnName="tags_ID")
     * })
     */
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

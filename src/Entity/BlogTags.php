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
    private $blog;

    /**
     * @var \Tags
     *
     * @ORM\ManyToOne(targetEntity="Tags")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tag_id", referencedColumnName="tags_ID")
     * })
     */
    private $tag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlog(): ?Blogs
    {
        return $this->blog ?? null;
    }

    public function setBlog(?Blogs $blog): self
    {
        $this->blog = $blog;

        return $this;
    }

    public function getTag(): ?Tags
    {
        return $this->tag ?? null;
    }

    public function setTag(?Tags $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}

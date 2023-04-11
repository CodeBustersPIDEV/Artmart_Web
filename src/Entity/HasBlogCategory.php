<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HasBlogCategory
 *
 * @ORM\Table(name="has_blog_category", indexes={@ORM\Index(name="blog_id", columns={"blog_id"}), @ORM\Index(name="category_id", columns={"category_id"})})
 * @ORM\Entity
 */
class HasBlogCategory
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
     * @var \Blogcategories
     *
     * @ORM\ManyToOne(targetEntity="Blogcategories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="categories_ID")
     * })
     */
    private $category_id;

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

    public function getCategory(): ?Blogcategories
    {
        return $this->category_id ?? null;
    }

    public function setCategory(?Blogcategories $category): self
    {
        $this->category_id = $category;

        return $this;
    }
}

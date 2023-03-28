<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MediaRepository;

// /**
//  * Media
//  *
//  * @ORM\Table(name="media", indexes={@ORM\Index(name="blog_id", columns={"blog_id"})})
//  * @ORM\Entity
//  */
/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
{
    /**
     * @var int
     *
     * @ORM\Column(name="media_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mediaId;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=false)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255, nullable=false)
     */
    private $fileType;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255, nullable=false)
     */
    private $filePath;

    /**
     * @var \Blogs
     *
     * @ORM\ManyToOne(targetEntity="Blogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="blog_id", referencedColumnName="blogs_ID")
     * })
     */
    private $blog_id;

    public function getMediaId(): ?int
    {
        return $this->mediaId;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
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

    public function getBlogId(): ?Blogs
    {
        return $this->blog_id;
    }

    public function setBlogId(?Blogs $blog_id): self
    {
        $this->blog_id = $blog_id;

        return $this;
    }
}

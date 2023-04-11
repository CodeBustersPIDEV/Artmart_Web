<?php

namespace App\Controller;

use App\Entity\Blogcategories;
use App\Entity\Blogs;
use App\Entity\BlogTags;
use App\Entity\HasBlogCategory;
use App\Entity\Media;
use App\Entity\Tags;
use App\Form\BlogsType;
use App\Repository\BlogcategoriesRepository;
use App\Repository\BlogsRepository;
use App\Repository\HasBlogCategoryRepository;
use App\Repository\HasTagRepository;
use App\Repository\MediaRepository;
use App\Repository\TagsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/blogs')]
class BlogsController extends AbstractController
{
    private $filesystem;
    private MediaRepository $mediaRepository;
    private HasBlogCategoryRepository $hasBlogCategoryRepository;
    private TagsRepository $tagsRepository;
    private BlogcategoriesRepository $blogCategoryRepository;
    private HasTagRepository $hasTagRepository;

    public function __construct(Filesystem $filesystem, MediaRepository $mediaRepository, BlogcategoriesRepository $blogCategoryRepository, HasBlogCategoryRepository $hasBlogCategoryRepository, TagsRepository $tagsRepository, HasTagRepository $hasTagRepository)
    {
        $this->filesystem = $filesystem;
        $this->mediaRepository = $mediaRepository;
        $this->hasBlogCategoryRepository = $hasBlogCategoryRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->tagsRepository = $tagsRepository;
        $this->hasTagRepository = $hasTagRepository;
    }

    public function uploadImage(UploadedFile $file, Media $media, Blogs $addedBlog, $edit): void
    {
        $destinationFilePath = $this->getParameter('destinationPath');
        // Get the original filename of the uploaded file
        $filename = $file->getClientOriginalName();
        if (!is_uploaded_file($file->getPathname())) {
            throw new FileException('File was not uploaded via HTTP POST.');
        }

        if (!is_dir($destinationFilePath)) {
            // Create the directory
            mkdir($destinationFilePath, 0777, true);
        }
        // Move the uploaded file to the destination
        $file->move($destinationFilePath, $filename);
        if ($edit === false) {
            $this->mediaRepository->save($media, $file, $addedBlog, true);
        } else {
            $this->mediaRepository->edit($media, $file, true);
        }
    }

    public function editTagsToBlog(Blogs $blog, string $tags, $alreadyTags): void
    {
        $tagNames = explode('#', $tags);
        foreach ($tagNames as $tagName) {
            if ($tagName === '') {
                continue;
            }
            $tag = $this->tagsRepository->findOneBy(['name' => $tagName]);
            if ($tag === null) {
                $tag = new Tags();
                $tag->setName($tagName);
                $this->tagsRepository->save($tag, true);
            }
            $tagAlreadyExists = false;
            foreach ($alreadyTags as $alreadyTag) {
                if ($alreadyTag->getTag() === $tag) {
                    $tagAlreadyExists = true;
                    break;
                }
            }
            if (!$tagAlreadyExists) {
                $hasTag = new BlogTags();
                $hasTag->setBlog($blog);
                $hasTag->setTag($tag);
                $this->hasTagRepository->save($hasTag, true);
            }
        }
    }



    public function addTagsToBlog(Blogs $blog, string $tags): void
    {
        $tagNames = explode('#', $tags);
        foreach ($tagNames as $tagName) {
            $tag = $this->tagsRepository->findOneBy(['name' => $tagName]);
            if ($tag === null && $tagName != "") {
                $newTag = new Tags();
                $addedtag = new Tags();
                $newTag->setName($tagName);
                $this->tagsRepository->save($newTag, true);
                $addedtag = $this->tagsRepository->findOneBy(['name' => $tagName]);
                if ($addedtag != null) {
                    $hasTag = new BlogTags();
                    $hasTag->setBlog($blog);
                    $hasTag->setTag($addedtag);
                    $this->hasTagRepository->save($hasTag, true);
                }
            } else {
                if ($tag !== null) {
                    $hasTag = new BlogTags();
                    $hasTag->setBlog($blog);
                    $hasTag->setTag($tag);
                    $this->hasTagRepository->save($hasTag, true);
                }
            }
        }
    }



    // ************************************************************************************************************************************************
    // ************************************************************************************************************************************************



    #[Route('/', name: 'app_blogs_index', methods: ['GET'])]
    public function index(BlogsRepository $blogsRepository): Response
    {
        return $this->render('blogs/index.html.twig', [
            'blogs' => $blogsRepository->findAll(),
        ]);
    }

    #[Route('/admin', name: 'app_blogs_admin', methods: ['GET'])]
    public function adminIndex(BlogsRepository $blogsRepository): Response
    {


        return $this->render('blogs/admin.html.twig', [
            'blogs' => $blogsRepository->findAll(),
            'blogCategories' => $this->blogCategoryRepository->findAll(),
            'tags' => $this->tagsRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_blogs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BlogsRepository $blogsRepository): Response
    {
        $blog = new Blogs();
        $addedBlog = new Blogs();
        $cat = new Blogcategories();
        $hasCategory = new HasBlogCategory();
        $media = new Media();
        $strTags = "";
        $tt = "";

        $edit = false;
        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $cat = $form->get('category')->getData();
            $tags = $form->get('tags')->getData();
            $title = $form->get('title')->getData();
            $addedTags = $_POST['addedTags'];
            foreach ($tags as $tag) {

                $strTags = $strTags . "#" . $tag->getName();
            }
            $tt = $strTags . $addedTags;
            $blogsRepository->save($blog, true);

            $addedBlog = $blogsRepository->findOneByTitle($title);

            $hasCategory->setBlog($addedBlog);
            $hasCategory->setCategory($cat);

            $this->hasBlogCategoryRepository->save($hasCategory, true);
            $this->addTagsToBlog($addedBlog, $tt);
            $this->uploadImage($file, $media, $addedBlog, $edit);

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('blogs/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
            'blog_media' => null
        ]);
    }

    #[Route('/{blogs_ID}', name: 'app_blogs_show', methods: ['GET'])]
    public function show(Blogs $blog, MediaRepository $mediaRepository, HasBlogCategoryRepository $hasBlogCategoryRepository, HasTagRepository $hasTagRepository): Response
    {
        return $this->render('blogs/show.html.twig', [
            'blog' => $blog,
            'blog_media' => $mediaRepository->findOneMediaByBlogID($blog->getBlogsId()),
            'blog_cat' => $hasBlogCategoryRepository->findOneByBlogID($blog->getBlogsId()),
            'blog_tags' => $hasTagRepository->findAllBlogsByBlogID($blog->getBlogsId())
        ]);
    }

    #[Route('/{blogs_ID}/edit', name: 'app_blogs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blogs $blog, BlogsRepository $blogsRepository, MediaRepository $mediaRepository, $blogs_ID): Response
    {
        $edit = true;
        $media = new Media();
        $cat = new Blogcategories();
        $hasCat = new HasBlogCategory();
        $strTags = "";
        $tt = "";

        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $mediaRepository->findOneMediaByBlogID($blog->getBlogsId());
            $alreadyTags = $this->hasTagRepository->findAllBlogsByBlogID($blog->getBlogsId());
            $hasCat = $this->hasBlogCategoryRepository->findOneByBlogID($blogs_ID);
            $cat = $form->get('category')->getData();
            $tags = $form->get('tags')->getData();
            $addedTags = $_POST['addedTags'];
            $file = $form->get('file')->getData();

            // $mediaRepository->deleteFile($media->getFileName());
            if ($file != null) {
                $this->uploadImage($file, $media, $blog, $edit);
            }

            $addedTags = $_POST['addedTags'];
            foreach ($tags as $tag) {

                $strTags = $strTags . "#" . $tag->getName();
            }
            $tt = $strTags . $addedTags;

            if ($hasCat === null) {
                $hasCat = new HasBlogCategory();
                $hasCat->setCategory($cat);
                $hasCat->setBlog($blog);
            } else {
                $hasCat->setCategory($cat);
            }
            $this->editTagsToBlog($blog, $tt, $alreadyTags);
            $blogsRepository->save($blog, true);
            $this->hasBlogCategoryRepository->save($hasCat, true);

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blogs/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
            'blog_media' => $mediaRepository->findOneMediaByBlogID($blog->getBlogsId()),
        ]);
    }

    #[Route('/{blogs_ID}', name: 'app_blogs_delete', methods: ['POST'])]
    public function delete(Request $request, Blogs $blog, BlogsRepository $blogsRepository, MediaRepository $mediaRepository, $blogs_ID): Response
    {
        $media = new Media();
        $hasCat = new HasBlogCategory();
        $media = $mediaRepository->findOneMediaByBlogID($blogs_ID);
        $hasCat = $this->hasBlogCategoryRepository->findOneByBlogID($blogs_ID);
        if ($this->isCsrfTokenValid('delete' . $blog->getBlogsID(), $request->request->get('_token'))) {
            $blogsRepository->remove($blog, true);
            $mediaRepository->remove($media, true);
            // $this->hasBlogCategoryRepository->remove($hasCat, true);
        }

        return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
    }
}

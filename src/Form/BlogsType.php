<?php

namespace App\Form;

use App\Entity\Blogcategories;
use App\Entity\Blogs;
use App\Entity\Tags;
use App\Entity\User;
use App\Repository\BlogcategoriesRepository;
use App\Repository\HasBlogCategoryRepository;
use App\Repository\HasTagRepository;
use App\Repository\MediaRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;


class BlogsType extends AbstractType
{
    private UserRepository $UserRepository;
    private BlogcategoriesRepository $blogcategoriesRepository;
    private HasBlogCategoryRepository $hasBlogCategoryRepository;
    private TagsRepository $tagsRepository;
    private HasTagRepository $hasTagRepository;
    private MediaRepository $mediaRepository;



    public function __construct(UserRepository $UserRepository, BlogcategoriesRepository $blogcategoriesRepository, HasBlogCategoryRepository $hasBlogCategoryRepository, TagsRepository $tagsRepository, HasTagRepository $hasTagRepository, MediaRepository $mediaRepository)
    {
        $this->UserRepository = $UserRepository;
        $this->blogcategoriesRepository = $blogcategoriesRepository;
        $this->tagsRepository = $tagsRepository;
        $this->mediaRepository = $mediaRepository;
        $this->hasBlogCategoryRepository = $hasBlogCategoryRepository;
        $this->hasTagRepository = $hasTagRepository;
    }

    public function getBlogData($blog)
    {
        $hBlogTags = $this->hasTagRepository->findAllBlogsByBlogID($blog->getBlogsId()) ?? null;
        $tags = [];
        foreach ($hBlogTags as $hBlogtag) {
            array_push($tags, $hBlogtag->getTag());
        }
        return $tags;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $blog = $options['data'] ?? null;
        $media = $this->mediaRepository->findOneMediaByBlogID($blog->getBlogsId()) ?? null;
        $hBlogCat = $this->hasBlogCategoryRepository->findOneByBlogID($blog->getBlogsId()) ?? null;
        $tags = $this->getBlogData($blog);


        $builder
            ->add('title')
            ->add('content')
            ->add('category',  EntityType::class, [
                'mapped' => false,
                'class' => Blogcategories::class,
                'choice_label' => 'name',
                'choices' => $this->blogcategoriesRepository->findAll(),
                'placeholder' => 'Choose an option', // optional
                'invalid_message' => 'Please select a valid category.',
                'constraints' => [
                    new NotNull(message: "Please select a valid category."),
                    new NotBlank(message: "Please select a valid category."),
                ],
                'data' => $hBlogCat ? $hBlogCat->getCategory() : null
            ])
            ->add('tags', EntityType::class, [
                'mapped' => false,
                'class' => Tags::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'choices' => $this->tagsRepository->findAll(),
                'placeholder' => 'Choose an option', // optional
                'invalid_message' => 'Please select at least one valid tag.',
                'constraints' => [
                    new NotNull(message: "Please select at least one valid tag."),
                    new NotBlank(message: "Please select at least one valid tag."),
                ],
                'data' => $tags,
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input', 'style' => 'margin:5px;'];
                }
            ])
            ->add('addTags', null, ['mapped' => false])
            // ->add('addedTags', null, ['mapped' => false, 'attr'  =>  ['style' => 'display:none;', 'value']])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choices' => $this->UserRepository->findAll(),
                'choice_label' => 'name',
                'placeholder' => 'Choose an option', // optional
            ])
            ->add('file', FileType::class, [
                'label' => 'Choose a file',
                'mapped' => false,
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blogs::class,
        ]);
    }
}

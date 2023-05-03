<?php

namespace App\Form;

use App\Entity\Blogcategories;
use App\Repository\BlogcategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogCategoryType extends AbstractType
{
  private BlogcategoriesRepository $blogcategoriesRepository;

  public function __construct(BlogcategoriesRepository $blogcategoriesRepository)
  {
    $this->blogcategoriesRepository = $blogcategoriesRepository;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name');
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => null,
    ]);
  }

  public function getBlockPrefix()
  {
    return 'blog_category';
  }
}

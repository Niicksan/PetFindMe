<?php

namespace PetFindMeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('town')
            ->add('phone')
            ->add('isFound')
            ->add('content');
            /*->add('dateAdded')
            ->add('authorId')
            ->add('author')*/
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PetFindMeBundle\Entity\Article'
        ));
    }
}

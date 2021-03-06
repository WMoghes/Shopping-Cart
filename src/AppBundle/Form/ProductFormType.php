<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productName')
            ->add('productDescription')
            ->add('productImage', FileType::class, ['label' => 'Image for product'])
            ->add('productPrice')
            ->add('productQuantity')
            ->add('isPublished');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => 'AppBundle\Entity\Product'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_product_form_type';
    }
}

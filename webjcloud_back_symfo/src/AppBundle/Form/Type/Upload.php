<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class Upload extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
         * The file does not need to be added as "file" because it is referred
         * in the validation for File and SF will automatically know it is file.
         */
        return $builder
            ->add("files", 'collection', array(
                'type'=>new FileType(),
                'allow_add'=>true,
                'data'=>array(new BundleEntityFile(),
                    new BundleEntityFile())
            ))
            ->add('save', 'submit');
    }

    public function getName()
    {
        return "files";
    }
}
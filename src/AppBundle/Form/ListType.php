<?php
namespace AppBundle\Form;

use AppBundle\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
        	    'required'	    => true,
        	    'label'	    => 'List Name'
            ));

    }
} 
?>
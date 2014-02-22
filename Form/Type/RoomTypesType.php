<?php
namespace Ydle\HubBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoomTypesType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', 'text', array('required' => true))
                ->add('description', 'textarea', array('required' => false))
                ->add('is_active', 'checkbox', array('label' => 'Actif ?', 'required' => false))
        ;
    }

    public function getName()
    {
        return 'room_types';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ydle\HubBundle\Entity\RoomType',
        ));
    }
}
?>

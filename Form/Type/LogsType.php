<?php
/*
  This file is part of Ydle.

    Ydle is free software: you can redistribute it and/or modify
    it under the terms of the GNU  Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ydle is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU  Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Ydle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Ydle\HubBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LogsType extends AbstractType
{    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeChoices = array(
            'all' => 'All',
            'info' => 'Info',
            'error' => 'Error',
            'warning' => 'Warning',
        );
        $sourcesChoices = array(
            'all' => 'All',
            'api' => 'Api',
            'hub' => 'Hub',
            'master' => 'Master',
            'nodes' => 'Nodes',
        );
        $builder
                ->add('type', 'choice', array(
                    'choices' => $typeChoices))                 
                ->add('source', 'choice', array(
                    'choices' => $sourcesChoices))
        ;
    }

    public function getName()
    {
        return 'logsfilter_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ydle\HubBundle\Entity\Logs',
            'csrf_protection'   => false,
        ));
    }
}
?>

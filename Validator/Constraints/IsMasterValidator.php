<?php
namespace Ydle\HubBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class IsMasterValidator extends ConstraintValidator
{  
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function validate($value, Constraint $constraint)
    {
        $masterId = $this->container->getParameter('master_id');
        if ($value == $masterId) {
            $this->context->addViolation($constraint->message, array('%string%' => $value));
        }
    }
}
?>

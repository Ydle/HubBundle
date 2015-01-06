<?php    
namespace Ydle\HubBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsMaster extends Constraint
{
    public $message = 'This code is already used by the master'; 
    
    
    public function validatedBy()
    {
        return 'master_validator';
    }
}
?>

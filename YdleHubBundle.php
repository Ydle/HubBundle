<?php

namespace Ydle\HubBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class YdleHubBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

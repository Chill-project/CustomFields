<?php

namespace Chill\CustomFieldsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Chill\CustomFieldsBundle\DependencyInjection\CustomFieldCompilerPass;

class ChillCustomFieldsBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CustomFieldCompilerPass());
    }
}

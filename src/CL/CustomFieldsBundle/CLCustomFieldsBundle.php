<?php

namespace CL\CustomFieldsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use CL\CustomFieldsBundle\DependencyInjection\CustomFieldCompilerPass;

class CLCustomFieldsBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CustomFieldCompilerPass());
    }
}

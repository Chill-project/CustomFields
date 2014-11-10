<?php


namespace Chill\CustomFieldsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('chill.custom_field.provider')) {
            throw new \LogicException('service chill.custom_field.provider '
                    . 'is not defined.');
        }
        
        $definition = $container->getDefinition(
            'chill.custom_field.provider'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'chill.custom_field'
        ); 
        
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addCustomField',
                    array(new Reference($id), $attributes["type"])
                );
            }
        }
    }

}

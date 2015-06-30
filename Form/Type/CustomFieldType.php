<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chill\CustomFieldsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Chill\CustomFieldsBundle\Form\DataTransformer\JsonCustomFieldToArrayTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Chill\CustomFieldsBundle\Form\AdressType;
use Chill\CustomFieldsBundle\Service\CustomFieldProvider;
use Chill\CustomFieldsBundle\Form\DataTransformer\CustomFieldDataTransformer;
use Chill\CustomFieldsBundle\Entity\CustomFieldsGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFieldType extends AbstractType
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     *
     * @var CustomFieldCompiler 
     */
    private $customFieldCompiler;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, CustomFieldProvider $compiler)
    {
        $this->om = $om;
        $this->customFieldCompiler = $compiler;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['group']->getCustomFields() as $cf) {
            $this->customFieldCompiler
                    ->getCustomFieldByType($cf->getType())
                    ->buildForm($builder, $cf);
        }
    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver
              ->setRequired(array('group'))
              ->addAllowedTypes(array('group' => 
                 array('Chill\CustomFieldsBundle\Entity\CustomFieldsGroup')))
                ;
    }

    public function getName()
    {
        return 'custom_field';
    }

}
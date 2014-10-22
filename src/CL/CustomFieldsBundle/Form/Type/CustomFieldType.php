<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\CustomFieldsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use CL\CustomFieldsBundle\Form\DataTransformer\JsonCustomFieldToArrayTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use CL\CustomFieldsBundle\Form\AdressType;
use CL\CustomFieldsBundle\CustomFields\CustomFieldCompiler;
use CL\CustomFieldsBundle\Form\DataTransformer\CustomFieldDataTransformer;

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
    public function __construct(ObjectManager $om, CustomFieldCompiler $compiler)
    {
        $this->om = $om;
        $this->customFieldCompiler = $compiler;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customFields = $this->om
                ->getRepository('CLCustomFieldsBundle:CustomField')
                ->findAll();

        foreach ($customFields as $cf) {

            $builder->add(
                    $builder->create(
                        $cf->getSlug(), 
                        $this->customFieldCompiler
                                ->getCustomFieldByType($cf->getType())
                                ->buildFormType($builder, $cf),
                        array('mapped' => true)
                            )
                    ->addModelTransformer(new CustomFieldDataTransformer(
                            $this->customFieldCompiler
                                ->getCustomFieldByType($cf->getType()),
                            $cf)
                            )
            );

//        if($cf->getType() === 'ManyToOne(Adress)') {
//            $builder->add($cf->getLabel(), 'entity', array(
//                'class' => 'CLCustomFieldsBundle:Adress',
//                'property' => 'data'
//            ));
//        } else if ($cf->getType() === 'ManyToOnePersist(Adress)') {
//            $builder->add($cf->getLabel(), new AdressType());
//        } else if($cf->getType() === 'ManyToMany(Adress)') {
//
//            $adress = $this->om
//                ->getRepository('CLCustomFieldsBundle:Adress')
//                ->findAll();
//
//            $adressId = array_map(
//            function($e) { return $e->getId(); },
//                $adress);
//
//            $adressLabel = array_map(
//            function($e) { return (string) $e; },
//                $adress);
//
//            $addressChoices = array_combine($adressId, $adressLabel);
//
//
//            $builder->add($cf->getLabel(), 'choice', array(
//                'choices' => $addressChoices,
//                'multiple' => true
//                ));
//        }
//        else {
//            $builder->add($cf->getLabel(), $cf->getType());
//        }
        }

        //$builder->addViewTransformer(new JsonCustomFieldToArrayTransformer($this->om));
    }
    
    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        $resolver
                //->addAllowedTypes(array('context' => 'string'))
                //->setRequired(array('context'))
                ;
    }

    public function getName()
    {
        return 'custom_field';
    }

}
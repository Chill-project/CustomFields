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

class CustomFieldType extends AbstractType
{
   /**
   * @var ObjectManager
   */
   private $om;

   /**
   * @param ObjectManager $om
   */
   public function __construct(ObjectManager $om)
   {
      $this->om = $om;
   }

   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $customFields = $this->om
      ->getRepository('CLCustomFieldsBundle:CustomField')
      ->findAll();

      foreach ($customFields as $cf) {
         $builder->add($cf->getLabel(), $cf->getType());
      }
      $builder->addViewTransformer(new JsonCustomFieldToArrayTransformer());
   }

   public function getName()
   {
      return 'custom_field';
   }
}
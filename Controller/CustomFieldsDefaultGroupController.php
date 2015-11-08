<?php

namespace Chill\CustomFieldsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Chill\CustomFieldsBundle\Entity\CustomFieldsDefaultGroup;

/**
 * CustomFieldsDefaultGroup controller.
 *
 */
class CustomFieldsDefaultGroupController extends Controller
{

    /**
     * Lists all CustomFieldsDefaultGroup entities.
     *
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $defaultGroups = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsDefaultGroup')->findAll();

        $form = $this->get('form.factory')
            ->createNamedBuilder(null, 'form', null, array(
                'method' => 'GET',
                'action' => $this->generateUrl('customfieldsdefaultgroup_set'),
                'csrf_protection' => false
            ))
            ->add('cFGroup', 'entity', array(
                'class' => 'ChillCustomFieldsBundle:CustomFieldsGroup',
                'property' => 'name[fr]'
            ))
            ->getForm();

        return $this->render('ChillCustomFieldsBundle:CustomFieldsDefaultGroup:list.html.twig', array(
            'defaultGroups' => $defaultGroups,
            'form' => $form->createView()
        ));
    }
}
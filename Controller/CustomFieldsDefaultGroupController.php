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

    /**
     * Set the CustomField Group with id $cFGroupId as default
     */
    public function setAGroupAsDefaultAction(Request $request)
    {
        $cFGroupId = $request->query->get('cFGroup');

        $em = $this->getDoctrine()->getManager();

        $cFGroup = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->findOneById($cFGroupId);

        if(!$cFGroup) {
            throw new Exception("No CF GROUP with ID".$cFGroupId, 1);
        }

        $cFDefaultGroup = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsDefaultGroup')
            ->findOneByEntity($cFGroup->getEntity());

        if($cFDefaultGroup) {
            $em->remove($cFDefaultGroup);
            $em->flush();
        }

        $newCFDefaultGroup = new CustomFieldsDefaultGroup();
        $newCFDefaultGroup->setCustomFieldsGroup($cFGroup);
        $newCFDefaultGroup->setEntity($cFGroup->getEntity());

        $em->persist($newCFDefaultGroup);
        $em->flush();

        return $this->redirect($this->generateUrl('customfieldsdefaultgroup'));
    }
}
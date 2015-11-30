<?php

namespace Chill\CustomFieldsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Chill\CustomFieldsBundle\Entity\CustomField;

/**
 * CustomField controller.
 *
 */
class CustomFieldController extends Controller
{

    /**
     * Creates a new CustomField entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CustomField();
        $form = $this->createCreateForm($entity, $request->query->get('type', null));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')
                  ->trans('The custom field has been created'));

            return $this->redirect($this->generateUrl('customfieldsgroup_show', 
                  array('id' => $entity->getCustomFieldsGroup()->getId())));
        } 
        
        $this->addFlash('error', $this->get('translator')
              ->trans("The custom field form contains errors"));

        return $this->render('ChillCustomFieldsBundle:CustomField:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CustomField entity.
     *
     * @param CustomField $entity The entity
     * @param string
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CustomField $entity, $type)
    {
        $form = $this->createForm('custom_field_choice', $entity, array(
            'action' => $this->generateUrl('customfield_create', 
                  array('type' => $type)),
            'method' => 'POST',
            'type' => $type,
            'group_widget' => ($entity->getCustomFieldsGroup()) ? 'hidden' :'entity'
        ));
        
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CustomField entity.
     *
     */
    public function newAction(Request $request)
    {
        $entity = new CustomField();
        
        //add the custom field group if defined in URL
        $cfGroupId = $request->query->get('customFieldsGroup', null);
        
        if ($cfGroupId !== null) {
            $cfGroup = $this->getDoctrine()->getManager()
                  ->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')
                  ->find($cfGroupId);
            if (!$cfGroup) {
                throw $this->createNotFoundException('CustomFieldsGroup with id '
                      . $cfGroupId.' is not found !');
            }
            $entity->setCustomFieldsGroup($cfGroup);
        }
        
        $form   = $this->createCreateForm($entity, $request->query->get('type'));

        return $this->render('ChillCustomFieldsBundle:CustomField:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CustomField entity.
     *
     * @deprecated is not used since there is no link to show action
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomField entity.');
        }

        return $this->render('ChillCustomFieldsBundle:CustomField:show.html.twig', array(
            'entity'      => $entity,        ));
    }

    /**
     * Displays a form to edit an existing CustomField entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomField entity.');
        }

        $editForm = $this->createEditForm($entity, $entity->getType());
        
        return $this->render('ChillCustomFieldsBundle:CustomField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CustomField entity.
    *
    * @param CustomField $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CustomField $entity, $type)
    {
        $form = $this->createForm('custom_field_choice', $entity, array(
            'action' => $this->generateUrl('customfield_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'type' => $type,
            'group_widget' => 'hidden' 
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CustomField entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomField entity.');
        }

        $editForm = $this->createEditForm($entity, $entity->getType());
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')
                  ->trans("The custom field has been updated"));

            return $this->redirect($this->generateUrl('customfield_edit', array('id' => $id)));
        }
        
        $this->addFlash('error', $this->get('translator')
              ->trans("The custom field form contains errors"));

        return $this->render('ChillCustomFieldsBundle:CustomField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
}

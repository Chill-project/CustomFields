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
     * Lists all CustomField entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ChillCustomFieldsBundle:CustomField')->findAll();
        
        //prepare form for new custom type
        $fieldChoices = array();
        foreach ($this->get('chill.custom_field.provider')->getAllFields() 
              as $key => $customType) {
            $fieldChoices[$key] = $customType->getName();
        }
        $form = $this->get('form.factory')
              ->createNamedBuilder(null, 'form', null, array(
                    'method' => 'GET',
                    'action' => $this->generateUrl('customfield_new'),
                    'csrf_protection' => false
                    ))
              ->add('type', 'choice', array(
                    'choices' => $fieldChoices
                   ))
              ->getForm();

        return $this->render('ChillCustomFieldsBundle:CustomField:index.html.twig', array(
            'entities' => $entities,
            'form'     => $form->createView()
        ));
    }
    
    
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

            return $this->redirect($this->generateUrl('customfield_show', array('id' => $entity->getId())));
        }

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
            'type' => $type
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
        $form   = $this->createCreateForm($entity, $request->query->get('type'));

        return $this->render('ChillCustomFieldsBundle:CustomField:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CustomField entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomField entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:CustomField:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
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
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:CustomField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
            'type' => $type
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

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, $entity->getType());
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('customfield_edit', array('id' => $id)));
        }

        return $this->render('ChillCustomFieldsBundle:CustomField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CustomField entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ChillCustomFieldsBundle:CustomField')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CustomField entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('customfield'));
    }

    /**
     * Creates a form to delete a CustomField entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customfield_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
}

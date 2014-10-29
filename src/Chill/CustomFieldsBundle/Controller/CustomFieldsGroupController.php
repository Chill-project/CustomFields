<?php

namespace Chill\CustomFieldsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Chill\CustomFieldsBundle\Entity\CustomFieldsGroup;
use Chill\CustomFieldsBundle\Form\CustomFieldsGroupType;

/**
 * CustomFieldsGroup controller.
 *
 */
class CustomFieldsGroupController extends Controller
{

    /**
     * Lists all CustomFieldsGroup entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->findAll();

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CustomFieldsGroup entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CustomFieldsGroup();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('customfieldsgroup_show', array('id' => $entity->getId())));
        }

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CustomFieldsGroup entity.
     *
     * @param CustomFieldsGroup $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CustomFieldsGroup $entity)
    {
        $form = $this->createForm(new CustomFieldsGroupType(), $entity, array(
            'action' => $this->generateUrl('customfieldsgroup_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CustomFieldsGroup entity.
     *
     */
    public function newAction()
    {
        $entity = new CustomFieldsGroup();
        $form   = $this->createCreateForm($entity);

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CustomFieldsGroup entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomFieldsGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CustomFieldsGroup entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomFieldsGroup entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CustomFieldsGroup entity.
    *
    * @param CustomFieldsGroup $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CustomFieldsGroup $entity)
    {
        $form = $this->createForm(new CustomFieldsGroupType(), $entity, array(
            'action' => $this->generateUrl('customfieldsgroup_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CustomFieldsGroup entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomFieldsGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('customfieldsgroup_edit', array('id' => $id)));
        }

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CustomFieldsGroup entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CustomFieldsGroup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('customfieldsgroup'));
    }

    /**
     * Creates a form to delete a CustomFieldsGroup entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customfieldsgroup_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

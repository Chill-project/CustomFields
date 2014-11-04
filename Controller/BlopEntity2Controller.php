<?php

namespace Chill\CustomFieldsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Chill\CustomFieldsBundle\Entity\BlopEntity2;
use Chill\CustomFieldsBundle\Form\BlopEntity2Type;

/**
 * BlopEntity2 controller.
 *
 */
class BlopEntity2Controller extends Controller
{

    /**
     * Lists all BlopEntity2 entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ChillCustomFieldsBundle:BlopEntity2')->findAll();

        return $this->render('ChillCustomFieldsBundle:BlopEntity2:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new BlopEntity2 entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BlopEntity2();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('blopentity2_show', array('id' => $entity->getId())));
        }

        return $this->render('ChillCustomFieldsBundle:BlopEntity2:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BlopEntity2 entity.
     *
     * @param BlopEntity2 $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BlopEntity2 $entity)
    {
        $form = $this->createForm(new BlopEntity2Type(), $entity, array(
            'action' => $this->generateUrl('blopentity2_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BlopEntity2 entity.
     *
     */
    public function newAction()
    {
        $entity = new BlopEntity2();
        $form   = $this->createCreateForm($entity);

        return $this->render('ChillCustomFieldsBundle:BlopEntity2:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BlopEntity2 entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity2')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlopEntity2 entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:BlopEntity2:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BlopEntity2 entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity2')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlopEntity2 entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:BlopEntity2:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BlopEntity2 entity.
    *
    * @param BlopEntity2 $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BlopEntity2 $entity)
    {
        $form = $this->createForm(new BlopEntity2Type(), $entity, array(
            'action' => $this->generateUrl('blopentity2_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BlopEntity2 entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity2')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlopEntity2 entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('blopentity2_edit', array('id' => $id)));
        }

        return $this->render('ChillCustomFieldsBundle:BlopEntity2:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BlopEntity2 entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity2')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BlopEntity2 entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('blopentity2'));
    }

    /**
     * Creates a form to delete a BlopEntity2 entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('blopentity2_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

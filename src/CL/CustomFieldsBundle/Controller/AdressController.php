<?php

namespace CL\CustomFieldsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CL\CustomFieldsBundle\Entity\Adress;
use CL\CustomFieldsBundle\Form\AdressType;

/**
 * Adress controller.
 *
 */
class AdressController extends Controller
{

    /**
     * Lists all Adress entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CLCustomFieldsBundle:Adress')->findAll();

        return $this->render('CLCustomFieldsBundle:Adress:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Adress entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Adress();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('adress_show', array('id' => $entity->getId())));
        }

        return $this->render('CLCustomFieldsBundle:Adress:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Adress entity.
     *
     * @param Adress $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Adress $entity)
    {
        $form = $this->createForm(new AdressType(), $entity, array(
            'action' => $this->generateUrl('adress_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Adress entity.
     *
     */
    public function newAction()
    {
        $entity = new Adress();
        $form   = $this->createCreateForm($entity);

        return $this->render('CLCustomFieldsBundle:Adress:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Adress entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CLCustomFieldsBundle:Adress')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Adress entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CLCustomFieldsBundle:Adress:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Adress entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CLCustomFieldsBundle:Adress')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Adress entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CLCustomFieldsBundle:Adress:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Adress entity.
    *
    * @param Adress $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Adress $entity)
    {
        $form = $this->createForm(new AdressType(), $entity, array(
            'action' => $this->generateUrl('adress_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Adress entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CLCustomFieldsBundle:Adress')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Adress entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('adress_edit', array('id' => $id)));
        }

        return $this->render('CLCustomFieldsBundle:Adress:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Adress entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CLCustomFieldsBundle:Adress')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Adress entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('adress'));
    }

    /**
     * Creates a form to delete a Adress entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adress_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

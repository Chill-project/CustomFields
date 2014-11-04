<?php

namespace Chill\CustomFieldsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Chill\CustomFieldsBundle\Entity\BlopEntity;
use Chill\CustomFieldsBundle\Form\BlopEntityType;
use Chill\CustomFieldsBundle\Form\AdressType;

/**
 * BlopEntity controller.
 *
 */
class BlopEntityController extends Controller
{

    public function addNewManyToOneAction($id, $key)
    {
        $em = $this->getDoctrine()->getManager();

        $customFields = $em->getRepository('ChillCustomFieldsBundle:CustomField')
            ->findAll();
   
        $customFieldsLablels = array_map(
            function($e) { return $e->getLabel(); },
            $customFields);

        $customFieldsByLabel = array_combine($customFieldsLablels, $customFields);

        if (array_key_exists($key,$customFieldsByLabel)) {
            $customFieldConfig = $customFieldsByLabel[$key];
            if($customFieldConfig->getType() === 'OneToMany(Adress)') {
                $manyToOneEntity = new AdressType();
                $form = $this->createCreateForm($manyToOneEntity);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($manyToOneEntity);
                    $em->flush();

                    $blopEntity = $this->om
                        ->getRepository('ChillCustomFieldsBundle:CustomField')
                        ->findOneById($id);

                    $blopEntityCustomFieldArray = json_decode($blopEntity->getCustomField());
                    $blopEntityCustomFieldArray[$key][] = $manyToOneEntity->getId();
                }
            } else {
                // PAS MANY TO ONE
                throw new Exception("Error Processing Request", 1);
            }
        } else {
            // PAS RENSEIGNE COMME CF
            throw new Exception("Error Processing Request", 1);
        }
    }

    /**
     * Lists all BlopEntity entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ChillCustomFieldsBundle:BlopEntity')->findAll();

        return $this->render('ChillCustomFieldsBundle:BlopEntity:index.html.twig', array(
            'entities' => $entities,
        ));
    }

   public function cfSetAction($id,$key,$value)
   {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity')->find($id);
      echo $entity->cfSet($key,$value);
      var_dump($entity->getCustomField());
      $em->persist($entity);
      $em->flush();
      return null;//$entity->cfSet($key,$value);
   }

   public function cfGetAction($id,$key)
   {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity')->find($id);
      echo $entity->cfGet($key);
      return null;//return $entity->cfGet($key);
   }

    /**
     * Creates a new BlopEntity entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BlopEntity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('blopentity_show', array('id' => $entity->getId())));
        }

        return $this->render('ChillCustomFieldsBundle:BlopEntity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BlopEntity entity.
     *
     * @param BlopEntity $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BlopEntity $entity)
    {
        $form = $this->createForm(new BlopEntityType(), $entity, array(
            'action' => $this->generateUrl('blopentity_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));



        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BlopEntity entity.
     *
     */
    public function newAction()
    {
        $entity = new BlopEntity();
        $form   = $this->createCreateForm($entity);

        return $this->render('ChillCustomFieldsBundle:BlopEntity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BlopEntity entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlopEntity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:BlopEntity:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BlopEntity entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlopEntity entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ChillCustomFieldsBundle:BlopEntity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BlopEntity entity.
    *
    * @param BlopEntity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BlopEntity $entity)
    {
        $form = $this->createForm(new BlopEntityType(), $entity, array(
            'action' => $this->generateUrl('blopentity_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BlopEntity entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlopEntity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('blopentity_edit', array('id' => $id)));
        }

        return $this->render('ChillCustomFieldsBundle:BlopEntity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BlopEntity entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ChillCustomFieldsBundle:BlopEntity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BlopEntity entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('blopentity'));
    }

    /**
     * Creates a form to delete a BlopEntity entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('blopentity_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
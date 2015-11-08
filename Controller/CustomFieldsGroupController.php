<?php

namespace Chill\CustomFieldsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query;
use Chill\CustomFieldsBundle\Entity\CustomFieldsGroup;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Chill\CustomFieldsBundle\Form\DataTransformer\CustomFieldsGroupToIdTransformer;
use Chill\CustomFieldsBundle\Entity\CustomFieldsDefaultGroup;

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

        $cfGroups = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->findAll();
        $defaultGroups = $this->getDefaultGroupsId();
        
        $makeDefaultFormViews = array();
        foreach ($cfGroups as $group) {
            if (!in_array($group->getId(), $defaultGroups)){
                $makeDefaultFormViews[$group->getId()] = $this->createMakeDefaultForm($group)->createView();
            }
        }

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:index.html.twig', array(
            'entities' => $cfGroups,
            'default_groups' => $defaultGroups,
            'make_default_forms' => $makeDefaultFormViews
        ));
    }
    
    /**
     * Get an array of CustomFieldsGroupId which are marked as default
     * for their entity
     * 
     * @return int[]
     */
    private function getDefaultGroupsId()
    {
        $em = $this->getDoctrine()->getManager();
        
        $customFieldsGroupIds = $em->createQuery('SELECT g.id FROM '
                . 'ChillCustomFieldsBundle:CustomFieldsDefaultGroup d '
                . 'JOIN d.customFieldsGroup g')
                ->getResult(Query::HYDRATE_SCALAR);
        
        $result = array();
        foreach ($customFieldsGroupIds as $row) {
            $result[] = $row['id'];
        }
        
        return $result;
    }
    
    /**
     * create a form to make the group default
     * 
     * @param CustomFieldsGroup $group
     * @return \Symfony\Component\Form\Form
     */
    private function createMakeDefaultForm(CustomFieldsGroup $group = null)
    {
        return $this->createFormBuilder($group, array(
                    'method' => 'POST',
                    'action' => $this->generateUrl('customfieldsgroup_makedefault')
                ))
                ->add('id', 'hidden')
                ->add('submit', 'submit', array('label' => 'Make default'))
                ->getForm();
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
        $form = $this->createForm('custom_fields_group', $entity, array(
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
        
        $options = $this->getOptionsAvailable($entity->getEntity());

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:show.html.twig', array(
            'entity'      => $entity,
            'create_field_form' => $this->createCreateFieldForm($entity)->createView(),
            'options' => $options
        ));
    }
    
    /**
     * Return an array of available key option for custom fields group
     * on the given entity
     * 
     * @param string $entity the entity to filter
     */
    private function getOptionsAvailable($entity)
    {
        $options = $this->getParameter('chill_custom_fields.'
              . 'customizables_entities');
                
        foreach($options as $key => $definition) {
            if ($definition['class'] == $entity) {
                foreach ($definition['options'] as $key => $value) {
                    yield $key;
                }
            }
        }
          //    [$entity->getEntity()];
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

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        $form = $this->createForm('custom_fields_group', $entity, array(
            'action' => $this->generateUrl('customfieldsgroup_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    private function createCreateFieldForm(CustomFieldsGroup $customFieldsGroup)
    {
        
        $fieldChoices = array();
        foreach ($this->get('chill.custom_field.provider')->getAllFields() 
              as $key => $customType) {
            $fieldChoices[$key] = $customType->getName();
        }
        
        $customfield = (new CustomField())
              ->setCustomFieldsGroup($customFieldsGroup);
        
        $builder = $this->get('form.factory')
              ->createNamedBuilder(null, 'form', $customfield, array(
                    'method' => 'GET',
                    'action' => $this->generateUrl('customfield_new'),
                    'csrf_protection' => false
                    ))
              ->add('type', 'choice', array(
                    'choices' => $fieldChoices
                   ))
              ->add('customFieldsGroup', 'hidden')
              ->add('submit', 'submit');
        $builder->get('customFieldsGroup')
              ->addViewTransformer(new CustomFieldsGroupToIdTransformer(
                       $this->getDoctrine()->getManager()));
        
        return $builder->getForm();
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

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('customfieldsgroup_edit', array('id' => $id)));
        }

        return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
      
           ));
    }
    
    /**
     * Set the CustomField Group with id $cFGroupId as default
     */
    public function makeDefaultAction(Request $request)
    {
        
        $form = $this->createMakeDefaultForm(null);
        $form->handleRequest($request);

        $cFGroupId = $form->get('id')->getData();

        $em = $this->getDoctrine()->getManager();

        $cFGroup = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->findOneById($cFGroupId);

        if(!$cFGroup) {
            throw $this
                  ->createNotFoundException("customFieldsGroup not found with "
                        . "id $cFGroupId");
        }

        $cFDefaultGroup = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsDefaultGroup')
            ->findOneByEntity($cFGroup->getEntity());

        if($cFDefaultGroup) {
            $em->remove($cFDefaultGroup);
            $em->flush(); /*this is necessary, if not doctrine
             * will not remove old entity before adding a new one, 
             * and this leads to violation constraint of unique entity
             * in postgresql
             */
        } 
        
        $newCFDefaultGroup = new CustomFieldsDefaultGroup();
        $newCFDefaultGroup->setCustomFieldsGroup($cFGroup);
        $newCFDefaultGroup->setEntity($cFGroup->getEntity());

        $em->persist($newCFDefaultGroup);
        $em->flush();

        return $this->redirect($this->generateUrl('customfieldsgroup'));
    }
    
    /**
     * This function render the customFieldsGroup as a form.
     *
     * This function is for testing purpose !
     *
     * The route which call this action is not in Resources/config/routing.yml,
     * but in Tests/Fixtures/App/app/config.yml
     *
     * @param int $id
     */
    public function renderFormAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomFieldsGroups entity.');
        }
    
        $form = $this->createForm('custom_field', null, array('group' => $entity));
        $form->add('submit_dump', 'submit', array('label' => 'POST AND DUMP'));
        $form->add('submit_render','submit', array('label' => 'POST AND RENDER'));
        $form->handleRequest($request);
        
        $this->get('twig.loader')
        ->addPath(__DIR__.'/../Tests/Fixtures/App/app/Resources/views/',
            $namespace = 'test');
        
        if ($form->isSubmitted()) {
            if ($form->get('submit_render')->isClicked()) {
                return $this->render('ChillCustomFieldsBundle:CustomFieldsGroup:render_for_test.html.twig', array(
                    'fields' => $form->getData(),
                    'customFieldsGroup' => $entity
                ));
            }
            
            var_dump($form->getData());
            var_dump(json_enccode($form->getData()));
        }
    
        
    
        return $this
            ->render('@test/CustomField/simple_form_render.html.twig', array(
                'form' => $form->createView()
        ));
    
    }
}

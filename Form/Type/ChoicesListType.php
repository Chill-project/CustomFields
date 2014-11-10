<?php
namespace Chill\CustomFieldsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ChoicesListType extends AbstractType
{
    
    private $defaultLocale;
    
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $this->defaultLocale;
        
        $builder->add('name', 'translatable_string')
            ->add('active', 'checkbox', array(
                'required' => false,
                'empty_data' => true
            ))
            ->add('slug', 'hidden', array(
                
            ))
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($locale){
                $form = $event->getForm();
                $data = $event->getData();

                $formData = $form->getData();
                
                if (NULL === $formData['slug']) {
                    $slug = $form['name'][$locale]->getData();
                    $slug= strtolower($slug);
                    $slug= preg_replace('/[^a-zA-Z0-9 -]/','', $slug); // only take alphanumerical characters, but keep the spaces and dashes too...
                    $slug= str_replace(' ','-', $slug); // replace spaces by dashes
                    
                    $data['slug'] = $slug;
                    $event->setData($data);
                } else {
                    $data['slug'] = $formData['slug'];
                    $event->setData($data);
                }
            })
        ;
    }
    
    
    /*
     *
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'cf_choices_list';
    }

}
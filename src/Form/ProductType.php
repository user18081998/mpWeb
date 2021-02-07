<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('location')
            ->add('image');
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    protected function addElements(FormInterface $form, Category $category=null)
    {
        $form->add('category', EntityType::class, array(
            'required' => true,
            'data' => $category,
            'placeholder' => 'Select a Category...',
            'class' => 'App:Category'
        ));
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected City and convert it into an Entity
        $category = $this->em->getRepository('App:Category')->find($data['category']);

        $this->addElements($form, $category);
    }

    function onPreSetData(FormEvent $event) {
        $product = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the City is always empty
        $category = $product->getCategory() ? $product->getCategory() : null;

        $this->addElements($form, $category);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

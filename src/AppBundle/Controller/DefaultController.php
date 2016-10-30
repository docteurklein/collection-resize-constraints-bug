<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(TestType::class, [
            'translations' => [
                'fr' => 'default lang entry',
                'en' => 'english entry',
            ],
        ]);
        $form->handleRequest($request);

        dump($form->isValid(), $form->getData());

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            'valid' => $form->isValid(),
            'data' => $form->getData(),
        ]);
    }
}

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isActive', CheckboxType::class, [
                'required' => false,
            ])
            ->add('translations', CollectionType::class, [
                'entry_options' => [
                    'required' => false,
                ],
            ])
            ->add('submit', SubmitType::class)
        ;

        $builder->get('translations')->addEventListener(FormEvents::SUBMIT, function($event) {
            if (!$event->getForm()->getParent()->get('isActive')->getData()) {
                // product is not marked as active
                return;
            }
            // product is marked as active: we want to make sure its defaut translation (fr) is provided
            $form = $event->getForm();
            $form->add('fr', TextType::class, [
                'constraints' => [new NotBlank], // NotBlank is added to the "fr" entry ONLY
                'required' => false,
            ]);
        });
    }
}

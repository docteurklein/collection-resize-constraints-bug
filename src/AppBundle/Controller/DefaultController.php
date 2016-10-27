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
                'default entry'
            ],
        ]);
        $form->handleRequest($request);

        dump($form->isValid(), $form->getData());

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isValid', CheckboxType::class, [
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
            $form = $event->getForm();
            $form->add(0, TextType::class, [
                'constraints' => [new NotBlank],
                'required' => false,
            ]);
        });
    }
}

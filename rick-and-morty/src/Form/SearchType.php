<?php

namespace App\Form;

use App\EventListeners\SearchFieldTextSanitiseListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class SearchType
 * @package App\Form
 */
class SearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label'=>'Character Name',
                'constraints'=>[
                  new Regex('/[\w\s]+/')
                ],
                'required'=>false,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ]
            ])->add('gender',ChoiceType::class,[
                'label'=>'Gender',
                'required'=>false,
                'choices'=>[
                    'Any'=>'',
                    'Male'=>'Male',
                    'Female'=>'Female',
                    'Genderless'=>'Genderless',
                    'Unknown'=>'Unknown'
                ],
                'constraints'=>[
                    new Regex('/[A-Za-z]+/')
                ],
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ]
            ])->add('status',ChoiceType::class,[
                'label'=>'Status',
                'required'=>false,
                'choices'=>[
                    'Any'=>'',
                    'Alive'=>'Alive',
                    'Dead'=>'Dead',
                    'Unknown'=>'unknown'
                ],
                'constraints'=>[
                    new Regex('/[A-Za-z]+/')
                ],
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ]
            ])->add('species',ChoiceType::class,[
                'label'=>'Species',
                'required'=>false,
                'choices'=>[
                    'Any'=>'',
                    'Human'=>'Human',
                    'Alien'=>'Alien'
                ],
                'constraints'=>[
                    new Regex('/[A-Za-z]+/')
                ],
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ]
            ])->add('submit',SubmitType::class,[
                'label'=>'Filter Results',
                'attr'=>[
                    'class'=>'btn btn-primary'
                ]
            ]);

        // sanitise the data before it gets submitted
        $builder->get('name')->addEventSubscriber(new SearchFieldTextSanitiseListener());
        $builder->get('gender')->addEventSubscriber(new SearchFieldTextSanitiseListener());
        $builder->get('status')->addEventSubscriber(new SearchFieldTextSanitiseListener());
        $builder->get('species')->addEventSubscriber(new SearchFieldTextSanitiseListener());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

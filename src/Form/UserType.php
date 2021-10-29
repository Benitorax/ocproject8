<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur"
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
                'required' => $options['edit_user'] ? false : true,
                'constraints' => $options['edit_user'] ? [] : [
                    new NotBlank([], 'Vous devez saisir un mot de passe.'),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email'
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'rôle utilisateur' => 'ROLE_USER',
                    'rôle administrateur' => 'ROLE_ADMIN',
                ],
                'label' => 'Rôle',
                'attr'  =>  ['class' => 'form-control'],
                'data' => [$options['data']->getRoles()[0]]
            ])
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    return implode(', ', $rolesAsArray);
                },
                function ($rolesAsString) {
                    return explode(', ', $rolesAsString);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'edit_user' => false
        ]);
    }
}

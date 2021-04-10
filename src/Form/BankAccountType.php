<?php

namespace App\Form;

use App\Entity\BankAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BankAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankName', null, [
                'label' => 'Nom de banque',
                'trim' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plaît, tapez le nom de banque",
                    ]),
                ],
            ])
            ->add('numero', null, [
                'label' => 'N° de compte',
                'trim' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plaît, tapez le N° de compte",
                    ]),
                ],
            ])
            ->add('solde', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plaît, tapez le solde",
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BankAccount::class,
        ]);
    }
}

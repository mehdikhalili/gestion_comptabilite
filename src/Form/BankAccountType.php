<?php

namespace App\Form;

use App\Entity\BankAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankName', null, [
                'label' => 'Nom de banque'
            ])
            ->add('numero', null, [
                'label' => 'N° de compte'
            ])
            ->add('solde')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BankAccount::class,
        ]);
    }
}

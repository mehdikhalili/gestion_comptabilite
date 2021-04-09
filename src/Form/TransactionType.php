<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tireur')
            ->add('modeDePaiement')
            ->add('cheque')
            ->add('rib')
            ->add('libelle')
            ->add('note')
            ->add('debit')
            ->add('credit')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('bankAccount')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}

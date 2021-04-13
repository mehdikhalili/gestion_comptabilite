<?php

namespace App\Form;

use App\Entity\BankAccount;
use App\Entity\Transaction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankAccount', EntityType::class, [
                'class' => BankAccount::class,
                'choice_label' => function($bankAccount) {
                    return $bankAccount->getNom_Numero();
                },
                'multiple' => false,
                'expanded' => false,
                'label' => 'Compte bancaire',
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plaît, choisissez le compte bancaire",
                    ]),
                ],
                'trim' => true,
            ])
            ->add('tireur', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plaît, tapez le nom de titeur",
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom de titeur doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom de titeur ne peut pas comporter plus de {{limit}} caractères',
                    ]),
                ],
                'trim' => true,
            ])
            ->add('modeDePaiement', ChoiceType::class, [
                'choices' => [
                    'Chèque' => 'cheque',
                    'Virement' => 'virement',
                ],
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plaît, choisissez le mode de paiement",
                    ]),
                    new Choice([
                        'choices' => [
                            'cheque',
                            'virement',
                        ],
                        'message' => "La valeur que vous avez sélectionnée n'est pas un choix valide."
                    ])
                ],
            ])
            ->add('cheque', null, [
                'label' => 'Chèque',
                'trim' => true,
            ])
            ->add('rib', null, [
                'label' => 'RIB',
                'trim' => true,
            ])
            ->add('libelle', ChoiceType::class, [
                'label' => 'Libellé',
                'choices' => [
                    'Achat matériel informatique' => 'achat_materiel_informatique',
                    'Achat logiciel' => 'achat_logiciel',
                    "Frais d'électricité" => 'frais_electricite',
                    'Loyer' => 'loyer',
                    'Salaire' => 'salaire',
                    'Paiement de client' => 'paiement_client',
                ],
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plaît, choisissez le libellé",
                    ]),
                    new Choice([
                        'choices' => [
                            'achat_materiel_informatique',
                            'achat_logiciel',
                            'frais_electricite',
                            'loyer',
                            'salaire',
                            'paiement_client',
                        ],
                        'message' => "La valeur que vous avez sélectionnée n'est pas un choix valide."
                    ])
                ],
            ])
            ->add('debit', null, [
                'label' => 'Débit',
                'mapped' => false
            ])
            ->add('credit', null, [
                'label' => 'Crédit',
                'mapped' => false
            ])
            ->add('note', TextareaType::class, [
                'trim' => true,
                'constraints' => [
                    new Length([
                        'max' => 256,
                        'maxMessage' => 'La note ne peut pas comporter plus de {{limit}} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}

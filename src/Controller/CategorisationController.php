<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorisation")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class CategorisationController extends AbstractController
{
    /**
     * @Route("/", name="categorisation_index")
     */
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('categorisation/index.html.twig', [
            'achat_materiel_informatique' => $this->merge($transactionRepository, 'achat_materiel_informatique'),
            'achat_logiciel' => $this->merge($transactionRepository, 'achat_logiciel'),
            'frais_electricite' => $this->merge($transactionRepository, 'frais_electricite'),
            'loyer' => $this->merge($transactionRepository, 'loyer'),
            'paiement_client' => $this->merge($transactionRepository, 'paiement_client'),
            'salaire' => $this->merge($transactionRepository, 'salaire'),
        ]);
    }

    private function merge(TransactionRepository $transactionRepository, string $libelle) {
        return array_merge($transactionRepository->findByLibelleMontant($libelle), $transactionRepository->findByLibelleCount($libelle));
    }
}

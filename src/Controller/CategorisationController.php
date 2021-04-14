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
            'categories' => $transactionRepository->findByLibelle(),
        ]);
    }
}

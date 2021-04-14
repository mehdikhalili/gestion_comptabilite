<?php

namespace App\Controller;

use App\Repository\BankAccountRepository;
use App\Repository\TransactionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(BankAccountRepository $bankAccountRepository, TransactionRepository $transactionRepository): Response
    {
        //dd($transactionRepository->findByLastAddedLimited());
        return $this->render('home/home.html.twig', [
            'bankAccounts' => $bankAccountRepository->findAll(),
            'transactions' => $transactionRepository->findByLastAddedLimited(),
        ]);
    }
}

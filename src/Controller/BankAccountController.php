<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Form\BankAccountType;
use App\Repository\BankAccountRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bank/account")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class BankAccountController extends AbstractController
{
    /**
     * @Route("/", name="bank_account_index", methods={"GET"})
     */
    public function index(BankAccountRepository $bankAccountRepository): Response
    {
        return $this->render('bank_account/index.html.twig', [
            'bank_accounts' => $bankAccountRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="bank_account_new", methods={"GET","POST"})
     * @IsGranted("ROLE_COMPTABLE")
     */
    public function new(Request $request): Response
    {
        $bankAccount = new BankAccount();
        $form = $this->createForm(BankAccountType::class, $bankAccount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bankAccount);
            $entityManager->flush();

            $this->addFlash('success', 'Un compte bancaire a été ajouté avec succés');
            return $this->redirectToRoute('bank_account_index');
        }

        return $this->render('bank_account/new.html.twig', [
            'bank_account' => $bankAccount,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bank_account_show", methods={"GET"})
     */
    public function show(BankAccount $bankAccount): Response
    {
        return $this->render('bank_account/show.html.twig', [
            'bank_account' => $bankAccount,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bank_account_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_COMPTABLE")
     */
    public function edit(Request $request, BankAccount $bankAccount): Response
    {
        $form = $this->createForm(BankAccountType::class, $bankAccount, ['method'=> 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Un compte bancaire a été modifié avec succés');
            return $this->redirectToRoute('bank_account_index');
        }

        return $this->render('bank_account/edit.html.twig', [
            'bank_account' => $bankAccount,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bank_account_delete", methods={"DELETE"})
     * @IsGranted("ROLE_COMPTABLE")
     */
    public function delete(Request $request, BankAccount $bankAccount): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bankAccount->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bankAccount);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Un compte bancaire a été supprimé avec succés');
        return $this->redirectToRoute('bank_account_index');
    }
}

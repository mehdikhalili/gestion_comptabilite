<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transaction")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class TransactionController extends AbstractController
{
    /**
     * @Route("/", name="transaction_index", methods={"GET"})
     */
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactionRepository->findByLastAdded(),
        ]);
    }

    /**
     * @Route("/new", name="transaction_new", methods={"GET","POST"})
     * @IsGranted("ROLE_COMPTABLE")
     */
    public function new(Request $request): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->dataValidation($form, $transaction, 'new');

            $transaction->setCredit($form->get('credit')->getData());
            $transaction->setDebit($form->get('debit')->getData());

            // TODO : Changer le solde de compte bancaire
            $this->changeBankAccountSolde($form, $transaction, 'new');

            $transaction->setCreatedAt(new DateTime());
            $transaction->setUpdatedAt(new DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', "Une transaction a été ajouté avec succés");

            return $this->redirectToRoute('transaction_index');
        }

        return $this->renderNewEdit($form, $transaction, 'new');
    }

    /**
     * @Route("/{id}", name="transaction_show", methods={"GET"})
     */
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_COMPTABLE")
     */
    public function edit(Request $request, Transaction $transaction): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction, ['method'=> 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->dataValidation($form, $transaction, 'edit');

            // TODO : Changer le solde de compte bancaire
            $this->changeBankAccountSolde($form, $transaction, 'edit');

            $transaction->setUpdatedAt(new DateTime());

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Une transaction a été modifié avec succés");

            return $this->redirectToRoute('transaction_index');
        }

        return $this->renderNewEdit($form, $transaction, 'edit');
    }

    /**
     * @Route("/{id}", name="transaction_delete", methods={"POST"})
     * @IsGranted("ROLE_COMPTABLE")
     */
    public function delete(Request $request, Transaction $transaction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {

            if (!is_null($transaction->getCredit())) {
                $money = -$transaction->getCredit();
            }
            elseif (!is_null($transaction->getDebit())) {
                $money = $transaction->getDebit();
            }
            $bankAccount = $transaction->getBankAccount();
            $bankAccount->setSolde($bankAccount->getSolde() - $money);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transaction);
            $entityManager->flush();

            $this->addFlash('success', "Une transaction a été supprimé avec succés");
        }

        return $this->redirectToRoute('transaction_index');
    }


    private function dataValidation(FormInterface $form, Transaction $transaction, string $type)
    {
        $modeDePaiement = $form->get('modeDePaiement')->getData();
        $cheque = $form->get('cheque')->getData();
        $rib = $form->get('rib')->getData();
        $libelle = $form->get('libelle')->getData();
        $debit = $form->get('debit')->getData();
        $credit = $form->get('credit')->getData();

        if ($modeDePaiement === 'cheque' && is_null($cheque)) {
            $form->get('cheque')->addError(new FormError("S'il vous plaît, tapez le numero du chèque"));
            return $this->renderNewEdit($form, $transaction, $type);
        }
        elseif ($modeDePaiement === 'virement' && is_null($rib)) {
            $form->get('rib')->addError(new FormError("S'il vous plaît, entrez le RIB"));
            return $this->renderNewEdit($form, $transaction, $type);
        }
        if ($libelle === 'paiement_client' && is_null($debit)) {
            $form->get('debit')->addError(new FormError("S'il vous plaît, entrez le débit"));
            return $this->renderNewEdit($form, $transaction, $type);
        }
        elseif ($libelle !== 'paiement_client' && is_null($credit)) {
            $form->get('credit')->addError(new FormError("S'il vous plaît, entrez le crédit"));
            return $this->renderNewEdit($form, $transaction, $type);
        }

        if (($modeDePaiement === 'cheque' && !is_null($rib)) || ($modeDePaiement === 'virement' && !is_null($cheque)) || ($libelle === 'paiement_client' && !is_null($credit)) || ($libelle !== 'paiement_client' && !is_null($debit))) {
            $this->addFlash('danger', "N'essayez pas de changer les entrées");
            if ($type === 'new') {
                return $this->redirectToRoute('transaction_new');
            } else {
                return $this->redirectToRoute('transaction_edit', ['id' => $transaction->getId()]);
            }
            
        }
    }

    private function renderNewEdit(FormInterface $form, Transaction $transaction, string $type): Response
    {
        return $this->render('transaction/'.$type.'.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ]);
    }

    private function changeBankAccountSolde(FormInterface $form, Transaction $transaction, string $type)
    {
        $bankAccount = $transaction->getBankAccount();
        $debit = $form->get('debit')->getData();
        $credit = $form->get('credit')->getData();

        if ($form->get('libelle')->getData() === 'paiement_client') {
            $money = $debit;
        } else {
            $money = -$credit;
        }

        if ($type === 'new') {
            $currentSolde = $bankAccount->getSolde() + $money;
        }
        else {
            if (!is_null($transaction->getCredit())) {
                $oldMoney = -$transaction->getCredit();
            }
            elseif (!is_null($transaction->getDebit())) {
                $oldMoney = $transaction->getDebit();
            }
            
            $currentSolde = $bankAccount->getSolde() - $oldMoney + $money;
        }

        $bankAccount->setSolde($currentSolde);

        $transaction->setCredit($credit);
        $transaction->setDebit($debit);
    }
}

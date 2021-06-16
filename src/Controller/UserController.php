<?php

namespace App\Controller;

use App\Entity\Comptable;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/{type}/edit", name="user_edit", methods={"GET","PUT"})
     */
    public function edit(Request $request, string $type, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($type === 'directeur') {
            $this->denyAccessUnlessGranted("ROLE_DIRECTEUR");
            $user = $this->getUser();
            $pageTitle = 'Modifier votre infos personnelles';
        }
        elseif ($type === 'comptable') {
            $user = $this->getDoctrine()->getRepository(Comptable::class)->findAll()[0];
            $pageTitle = 'Modifier le trésorier';
        }
        else {
            throw new NotFoundHttpException();
        }
        
        $form = $this->createForm(UserType::class, $user, ['method'=> 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();
            $newPassword = $form->get('newPassword')->getData();

            // Check user password
            if (!$passwordEncoder->isPasswordValid($this->getUser(), $password)) {
                $this->addFlash('danger', "Votre mot de passe n'est pas correct");
                return $this->redirectToRoute('user_edit', ['type' => $type]);
            }

            // encode the new password
            if (!is_null($newPassword)) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $newPassword
                    )
                );
            }

            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash('success', 'Les informations ont été modifiés avec succés');
            return $this->redirectToRoute('user_edit', ['type' => $type]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'pageTitle' => $pageTitle
        ]);
    }
}

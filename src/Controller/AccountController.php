<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account", name="account_home")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * @Route("/account/update", name="account_update")
     */
    public function update(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneById($this->getUser()->getId());
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $success = true;
            $emailFound = $this->entityManager
                ->getRepository(User::class)
                ->findOneByEmail($user->getEmail());

            // si email existe et email différent email courant
            if (!empty($emailFound) && $emailFound->getEmail() !== $this->getUser()->getEmail()) {
                $success = false;
                $this->addFlash('danger', "Cet adresse e-mail est déjà utilisée.");
            }

            if (!$form->get('new_password')->isEmpty()) {
                if ($form->get('old_password')->isEmpty()) {
                    $success = false;
                    $this->addFlash('danger', "Votre mot de passe actuel doit être renseigné.");
                } else if (!$encoder->isPasswordValid($user, $form->get('old_password')->getData())) {
                    $success = false;
                    $this->addFlash('danger', "Votre mot de passe actuel est incorrect.");
                } else {
                    $hash = $encoder->encodePassword($user, $form->get('new_password')->getData());
                    $user->setPassword($hash);
                }
            }

            if ($success) {
                $this->entityManager->flush();
                $this->addFlash('success', "Vos informations ont bien été sauvegardées.");
            }
        }

        return $this->render('account/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

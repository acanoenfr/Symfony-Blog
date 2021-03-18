<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\PasswordReset;
use App\Entity\User;
use App\Form\PasswordForgotType;
use App\Form\PasswordResetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordController extends AbstractController
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
     * @Route("/forgot-password", name="password_forgot")
     */
    public function index(Request $request): Response
    {
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        $form = $this->createForm(PasswordForgotType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->findOneByEmail($form->get('email')->getData());

            if (!$user) {
                $this->addFlash('danger', "Cette adresse e-mail n'est associée à aucun utilisateur.");
            } else {
                $passwordReset = new PasswordReset();
                $passwordReset->setToken($this->generateToken());
                $passwordReset->setCreatedAt(new \DateTime());
                $passwordReset->setUser($user);

                $this->entityManager->persist($passwordReset);
                $this->entityManager->flush();

                $transport = new \Swift_SmtpTransport('localhost', 1025);
                $mailer = new \Swift_Mailer($transport);
                $message = (new \Swift_Message('Demande de réinitialisation de votre mot de passe'))
                    ->setFrom(['no-reply@acanoen.fr' => 'Le Blog - Robot'])
                    ->setTo([$passwordReset->getUser()->getEmail() => $passwordReset->getUser()->getFirstname()])
                    ->setBody(
                        "<p>Veuillez cliquer sur <a href='https://localhost:8000/reset-password/{$passwordReset->getToken()}'>ce lien</a> pour changer votre mot de passe.</p>",
                        "text/html; charset=UTF-8"
                    );
                $mailer->send($message);

                $this->addFlash('success', "Un lien de réinitialisation de votre mot de passe a été généré et envoyé à votre adresse e-mail.");
            }
        }

        return $this->render('password/forgot.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="password_reset")
     */
    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        $passwordReset = $this->entityManager
            ->getRepository(PasswordReset::class)
            ->findOneByToken($token);

        if (!$passwordReset) {
            $this->addFlash('danger', "Votre lien de réinitialisation n'existe pas ou plus. Merci d'en créer un nouveau.");
            return $this->redirectToRoute('password_forgot');
        }

        $now = new \DateTime();
        if ($now > $passwordReset->getCreatedAt()->modify('+ 2 hour')) {
            $this->addFlash('danger', 'Votre lien de réinitialisation a expiré. Merci de le renouveller.');
            return $this->redirectToRoute('password_forgot');
        }

        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('new_password')->getData();

            $hash = $encoder->encodePassword($passwordReset->getUser(), $new_pwd);
            $passwordReset->getUser()->setPassword($hash);

            $this->entityManager->remove($passwordReset);
            $this->entityManager->flush();

            $this->addFlash('success', "Votre mot de passe a bien été mis à jour.");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('password/reset.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    private function generateToken($length = 32)
    {
        return substr(
            str_shuffle(
                str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length/strlen($x))
                )
            ),1, $length);
    }
}

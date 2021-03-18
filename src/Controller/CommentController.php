<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/{id}/remove", name="comment_remove")
     */
    public function remove($id, EntityManagerInterface $entityManager): Response
    {
        $comment = $entityManager
            ->getRepository(Comment::class)
            ->findOneById($id);

        if (!$comment) {
            return $this->redirectToRoute('app_home');
        }

        if ($comment->getUser() !== $this->getUser()) {
            $this->addFlash('danger', "Ce commentaire ne vous appartient pas.");
            return $this->redirectToRoute('app_post_details', [
                'slug' => $comment->getPost()->getSlug()
            ]);
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', "Votre commentaire a bien été supprimé.");
        return $this->redirectToRoute('app_post_details', [
            'slug' => $comment->getPost()->getSlug()
        ]);
    }
}

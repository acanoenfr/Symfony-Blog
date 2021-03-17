<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
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
     * @Route("/admin/post", name="admin_post")
     */
    public function index(): Response
    {
        $posts = $this->entityManager
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('admin/post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/admin/post/add", name="admin_post_add")
     */
    public function add(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('file')->getData()) {
                $this->addFlash('success', "L'article doit avoir une image de couverture.");
                return $this->redirectToRoute('admin_post_add');
            }

            $file = $form->get('file')->getData();
            $newFilename = uniqid() . ".{$file->guessExtension()}";
            $post->setImage($newFilename);
            $destination = $this->getParameter('kernel.project_dir') . "/public/uploads/posts";
            $file->move($destination, $newFilename);

            $post->setCreatedAt(new \DateTime());

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', "L'article a bien été ajoutée.");
            return $this->redirectToRoute('admin_post');
        }

        return $this->render('admin/post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/post/{id}/edit", name="admin_post_edit")
     */
    public function edit($id, Request $request, Filesystem $filesystem): Response
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneById($id);

        if (!$post) {
            $this->addFlash('danger', "Article inconnu.");
            return $this->redirectToRoute('admin_post');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if (!empty($file)) {
                $destination = $this->getParameter('kernel.project_dir') . "/public/uploads/posts";
                $newFilename = uniqid() . ".{$file->guessExtension()}";
                $filesystem->remove($destination."/{$post->getImage()}");
                $post->setImage($newFilename);
                $file->move($destination, $newFilename);
            }

            $this->entityManager->flush();

            $this->addFlash('success', "L'article a bien été modifiée.");
            return $this->redirectToRoute('admin_post');
        }

        return $this->render('admin/post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/post/{id}/remove", name="admin_post_remove")
     */
    public function remove($id, Filesystem $filesystem): Response
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneById($id);

        if (!$post) {
            $this->addFlash('danger', "Article inconnu.");
            return $this->redirectToRoute('admin_post');
        }

        if (!empty($post->getImage())) {
            $destination = $this->getParameter('kernel.project_dir') . "/public/uploads/posts";
            $filesystem->remove($destination."/{$post->getImage()}");
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $this->addFlash('success', "L'article a bien été supprimée.");
        return $this->redirectToRoute('admin_post');
    }
}

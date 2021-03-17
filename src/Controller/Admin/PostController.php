<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $newFilename = uniqid() . "{$file->guessExtension()}";
            $post->setImage($newFilename);
            $destination = $this->getParameter('kernel.project_dir') . "/public/uploads/posts";
            $file->move($destination, $newFilename);

            $post->setCreatedAt(new \DateTime());

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', "L'article a bien été ajouté.");
            return $this->redirectToRoute('admin_post');
        }

        return $this->render('admin/post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/post/{id}/edit", name="admin_post_edit")
     */
    public function edit(Request $request): Response
    {
        return $this->render('admin/post/edit.html.twig');
    }

    /**
     * @Route("/admin/post/{id}/remove", name="admin_post_remove")
     */
    public function remove(): Response
    {
        return $this->redirectToRoute('admin_post');
    }
}

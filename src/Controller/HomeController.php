<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        $bestPosts = $this->entityManager
            ->getRepository(Post::class)
            ->findByIsBest(1);

        $lastPost = $this->entityManager
            ->getRepository(Post::class)
            ->findLastPostPublished();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'bestPosts' => $bestPosts,
            'lastPost' => $lastPost
        ]);
    }

    /**
     * @Route("/category/{slug}", name="app_category_details")
     */
    public function show_category($slug): Response
    {
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneBySlug($slug);

        if (!$category) {
            return $this->redirectToRoute('app_home');
        }

        dd($category);
    }

    /**
     * @Route("/post/{slug}", name="app_post_details")
     */
    public function show_post($slug): Response
    {
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBySlug($slug);

        if (!$post) {
            return $this->redirectToRoute('app_home');
        }

        dd($post);
    }
}

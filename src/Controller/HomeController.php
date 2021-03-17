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
}

<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
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
     * @Route("/admin/category", name="admin_category")
     */
    public function index(): Response
    {
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/category/add", name="admin_category_add")
     */
    public function add(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', "La catégorie a bien été ajoutée.");
            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/category/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="admin_category_edit")
     */
    public function edit($id, Request $request): Response
    {
        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneById($id);

        if (!$category) {
            $this->addFlash('danger', "Catégorie inconnue.");
            return $this->redirectToRoute('admin_category');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', "La catégorie a bien été mise à jour.");
            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/remove", name="admin_category_remove")
     */
    public function remove($id): Response
    {
        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneById($id);

        if (!$category) {
            $this->addFlash('danger', "Catégorie inconnue.");
            return $this->redirectToRoute('admin_category');
        }

        if (count($category->getPosts()) > 0) {
            $this->addFlash('danger', "Des articles sont présents dans cette catégorie, suppression impossible.");
            return $this->redirectToRoute('admin_category');
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash('success', "La catégorie a bien été supprimée.");
        return $this->redirectToRoute('admin_category');
    }
}

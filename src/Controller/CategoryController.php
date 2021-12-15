<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryCreateType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
	/**
	 * @Route("/categories", name="categories", methods={"GET", "POST", "DELETE"})
	 */
    public function categories(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $manager): Response
    {
		$category = new Category();

		$form = $this->createForm(CategoryCreateType::class, $category);
		$form->handleRequest($request);

		try {
			if ($form->isSubmitted() && $form->isValid()) {
				$manager->persist($category);
				$manager->flush();
			}
		} catch (\Exception $e) {
			$form->addError(new \Symfony\Component\Form\FormError($e->getMessage()));
		}

        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
			'form' => $form->createView()
        ]);
    }
}

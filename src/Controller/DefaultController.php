<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    /**
	 * @Route("/", name="liste_articles", methods={"GET"})
	 */
	public function listeArticles(ArticleRepository $articleRepository): Response
	{
		$articles = $articleRepository->findAll();
		
		return $this->render("default/index.html.twig", [
			"articles" => $articles
		]);
	}

	/**
	 * @Route("/{id}", name="vue_article", requirements={"id"="\d+"}, methods={"GET"})
	 */
	public function vueArticle(Article $article)
	{
		return $this->render("default/vue.html.twig", [
			"article" => $article
		]);
	}

	/**
	 * @Route("/article/ajouter", name="ajout_article")
	 */
	public function ajouter(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $manager)
	{
		$form = $this->createFormBuilder()
			->add("titre", TextType::class, [
				"label" => "Titre de l'article"
			])
			->add("contenu", TextareaType::class)
			->add("dateCreation", DateType::class, [
				"widget" => "single_text"
			])->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$article = new Article();
			$article->setTitre($form->get("titre")->getData());
			$article->setContenu($form->get("contenu")->getData());
			$article->setDateCreation($form->get("dateCreation")->getData());

			$category = $categoryRepository->findOneBy([
				"name" => "Sport"
			]);
			$article->addCategory($category);

			$manager->persist($article);
			$manager->flush();

			return $this->redirectToRoute("liste_articles");
		}

		return $this->render('default/ajout.html.twig', [
			"form" => $form->createView()
		]);
	}
}

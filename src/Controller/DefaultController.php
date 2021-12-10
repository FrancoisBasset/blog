<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
	 * @Route("/", name="liste_articles", methods={"GET"})
	 */
	public function listeArticles(): Response
	{
		$url1 = $this->generateUrl("vue_article", ["id" => 1]);
		$url2 = $this->generateUrl("vue_article", ["id" => 2]);
		$url3 = $this->generateUrl("vue_article", ["id" => 3]);
		$url4 = $this->generateUrl("vue_article", ["id" => 4]);

		$articles = [
			[
				"nom" => "Article 1",
				"url" => $url1
			],
			[
				"nom" => "Article 2",
				"url" => $url2
			],
			[
				"nom" => "Article 3",
				"url" => $url3
			],
			[
				"nom" => "Article 4",
				"url" => $url4
			]
		];
		
		return $this->render("default/index.html.twig", [
			"articles" => $articles
		]);
	}

	/**
	 * @Route("/{id}", name="vue_article", requirements={"id"="\d+"}, methods={"GET"})
	 */
	public function vueArticle($id)
	{
		return $this->render("default/vue.html.twig", [
			"id" => $id
		]);
	}
}

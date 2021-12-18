<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Service\VerificationComment;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
	/**
	 * @Route("/", name="liste_articles", methods={"GET"})
	 */
	public function listeArticles(ArticleRepository $articleRepository): Response
	{
		$articles = $articleRepository->findBy([
			'state' => 'publie'
		]);

		return $this->render('default/index.html.twig', [
			'articles' => $articles,
			'brouillon' => false
		]);
	}

	/**
	 * @Route("/{id}", name="vue_article", requirements={"id"="\d+"}, methods={"GET", "POST"})
	 */
	public function vueArticle(Article $article, Request $request, EntityManagerInterface $manager, VerificationComment $verifService)
	{
		$comment = new Comment();
		$comment->setArticle($article);

		$form = $this->createForm(CommentType::class, $comment);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ($verifService->commentaireNonAutorise($comment) === false) {
				$manager->persist($comment);
				$manager->flush();

				$this->redirectToRoute('vue_article', [
					'id' => $article->getId()
				]);
			}
		}

		return $this->render('default/vue.html.twig', [
			'article' => $article,
			'form' => $form->createView()
		]);
	}
}
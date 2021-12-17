<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Service\VerificationComment;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

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

	/**
	 * @Route("/article/ajouter", name="ajout_article")
	 * @Route("/article/{id}/edition", name="edition_article", requirements={"id"="\d+"}, methods={"GET", "POST"})
	 */
	public function ajouter(Article $article = null, Request $request, EntityManagerInterface $manager, LoggerInterface $logger)
	{
		if ($article == null) {
			$article = new Article();
		}

		$logger->info('Nous sommes passé ici');
		
		$form = $this->createForm(ArticleType::class, $article);

		/*$form = $this->createFormBuilder()
			->add('titre', TextType::class, [
				'label' => 'Titre de l'article'
			])
			->add('contenu', TextareaType::class)
			->add('dateCreation', DateType::class, [
				'widget' => 'single_text'
			])->getForm();*/

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/*$article = new Article();
			$article->setTitre($form->get('titre')->getData());
			$article->setContenu($form->get('contenu')->getData());
			$article->setDateCreation($form->get('dateCreation')->getData());

			$category = $categoryRepository->findOneBy([
				'name' => 'Sport'
			]);
			$article->addCategory($category);*/

			if ($form->get('brouillon')->isClicked()) {
				$article->setState('brouillon');
			} else {
				$article->setState('a publier');
			}

			if ($article->getId() === null) {
				$manager->persist($article);
			}
			
			$manager->flush();

			return $this->redirectToRoute('liste_articles');
		}

		return $this->render('default/ajout.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/article/brouillon", name="brouillon_article")
	 */
	public function brouillon(ArticleRepository $articleRepository)
	{
		$articles = $articleRepository->findBy([
			'state' => 'brouillon'
		]);

		return $this->render('default/index.html.twig', [
			'articles' => $articles,
			'brouillon' => true
		]);
	}
}

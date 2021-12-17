<?php

namespace App\Command;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:publish-article',
    description: 'Publie les articles à publier',
)]
class PublishArticleCommand extends Command
{
	private $articleRepository;
	private $manager;

	public function __construct(ArticleRepository $articleRepository, EntityManagerInterface $manager,  string $name = null)
	{
		$this->articleRepository = $articleRepository;
		$this->manager = $manager;

		parent::__construct($name);
	}

    protected function configure(): void
    {
        $this
			->setDescription('Publie les articles à publier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output, ): int
    {
        $io = new SymfonyStyle($input, $output);

		$articles = $this->articleRepository->findBy([
			'state' => 'a publier'
		]);

		foreach ($articles as $article) {
			$article->setState('publie');
		}

		$this->manager->flush();

        $io->success(count($articles) . ' articles publiés');

        return Command::SUCCESS;
    }
}

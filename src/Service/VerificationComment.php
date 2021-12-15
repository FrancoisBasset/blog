<?php

namespace App\Service;

use App\Entity\Comment;

class VerificationComment
{
	public function commentaireNonAutorise(Comment $comment)
	{
		$nonAutorise = [
			'mauvais',
			'nul',
			'pourri'
		];

		foreach ($nonAutorise as $word) {
			if (strpos($comment->getContenu(), $word)) {
				return true;
			}
		}

		return false;
	}
}
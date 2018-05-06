<?php

namespace App\Controller;

use App\Entity\{ Article, Comment };
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface as EM;

class CommentController extends Controller {

	/**
	 * Creates new comment
	 * @param int $article article id
	 */
	function write_submit(EM $em, Request $request, int $article) {
		$csrf_id = "comment-save";
		$csrf_token = $request->get("csrf_token");
		$csrf_valid = $this->isCsrfTokenValid($csrf_id, $csrf_token);

		if (!$csrf_valid) {
			return $this->redirectToRoute("article_view", [ "id" => $article ]);
		}

		if (false === $this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute("article_view", [ "id" => $article ]);
		}

		$comment = new Comment;
		$comment->author = $this->getUser();
		$comment->article = $em->find(Article::class, $article);
		$comment->content = $request->get("content");

		$em->persist($comment);
		$em->flush();

		return $this->redirectToRoute("article_view", [ "id" => $article ]);
	}

}

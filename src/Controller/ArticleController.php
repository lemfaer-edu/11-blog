<?php

namespace App\Controller;

use App\Entity\{ Article, Category, Tag };
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface as EM;

class ArticleController extends Controller {

	/**
	 * Renders page with article edit form
	 * @param int|null $id article id to edit
	 */
	function write(EM $em, int $id = null) {
		$entity = $id ? $em->find(Article::class, $id) : new Article;
		$categories = $em->getRepository(Category::class)->findAll();
		return $this->render("article/form.html.twig", compact("entity", "categories"));
	}

	/**
	 * Performs actions to create/edit article
	 * @param string <title> title
	 * @param int <category> category id
	 * @param string <content> content in html format
	 * @param string <tags> string of tags, separated by `,`
	 * @param string <csrf_token> token
	 * @param int|null $id article id to edit
	 */
	function write_submit(EM $em, Request $request, int $id = null) {
		$csrf_id = "article-save";
		$csrf_token = $request->get("csrf_token");
		$csrf_valid = $this->isCsrfTokenValid($csrf_id, $csrf_token);

		if (!$csrf_valid) {
			$this->redirectToRoute("blog_list");
		}

		$tag_repo = $em->getRepository(Tag::class);
		$tags = $tag_repo->add_tags($request->get("tags"));

		dump($tags);

		// $entity = $id ? $em->find(Article::class, $id) : new Article;
		// $entity->
	}

}

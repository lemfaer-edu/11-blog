<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
// use Symfony\Component\HttpFoundation\Response;
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
	 * @param int|null $id article id to edit
	 */
	function write_submit(int $id = null) {}

}

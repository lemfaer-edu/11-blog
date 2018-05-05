<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface as EM;

class BlogController extends Controller {

	/**
	 * Renders articles list page
	 * @param string $category name
	 * @param int $page for pagination, >0
	 */
	function list(EM $em, $category, int $page) {
		$repo = $em->getRepository(Article::class);

		$query_builder = $repo
			->createQueryBuilder("a")
			->innerJoin("a.category", "c")
			->innerJoin("a.author", "u");

		if ($category) {
			$query_builder
				->andWhere("c.name = :category")
				->setParameter("category", $category);
		}

		// if ($month) {
		// 	$start = DateTime::createFromFormat("Y-m|", $month);
		// 	$end = (clone $start)->modify("next month");

		// 	$query_builder
		// 		->andWhere("a.updated >= :start")
		// 		->andWhere("a.updated <= :end")
		// 		->setParameter("start", $start)
		// 		->setParameter("end", $end);
		// }

		$limit = 5;
		$offset = ($page-1) * $limit;

		$query_builder
			->orderBy("a.id", "desc")
			->setFirstResult($offset)
			->setMaxResults($limit);

		$result = $query_builder
			->getQuery()
			->getResult();

		dump($this->getUser());

		// return new Response("<body>" . count($result[0]->comments) . "</body>");
	}

}

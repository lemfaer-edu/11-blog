<?php

namespace App\Repository;

use App\Entity\Article;
use DOMDocument, DOMXpath, DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ArticleRepository extends ServiceEntityRepository {

	function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Article::class);
	}

	/**
	 * Selects count of all ent
	 * @param string|null $category name
	 * @return int
	 */
	function count($category) {
		$query_builder = $this
			->createQueryBuilder("a")
			->innerJoin("a.category", "c")
			->select("count(a)");

		if ($category) {
			$query_builder
				->andWhere("c.name = :category")
				->setParameter("category", $category);
		}

		$result = $query_builder
			->getQuery()
			->getSingleResult();

		$count = array_shift($result);
		$count = intval($count);

		return $count;
	}

	/**
	 * Loads article list info
	 * @param string|null $category name
	 * @param int $limit limit of tags to load
	 * @param int $offset offset count
	 */
	function list($category, int $limit, int $offset) {
		$query_builder = $this
			->createQueryBuilder("a")
			->innerJoin("a.category", "c")
			->innerJoin("a.author", "u")
			->addSelect("c")
			->addSelect("u");

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

		$query_builder
			->orderBy("a.id", "desc")
			->setFirstResult($offset)
			->setMaxResults($limit);

		return $query_builder
			->getQuery()
			->getResult();
	}

	/**
	 * Extracts description info from article content
	 * @param iterable $entities articles
	 * @param int $length description max length
	 * @return array, [ $img, $desc ]
	 */
	function description($entities, $length = 100) {
		// clear errors from non-well formatted/broken HTML
		libxml_use_internal_errors(true);

		foreach ($entities as $entity) {
			$dom = new DOMDocument;
			$dom->loadHTML($entity->content);
			$xpath = new DOMXpath($dom);

			$pquery = $xpath->query("//p");
			$iquery = $xpath->query("//img");

			if ($pquery->length) {
				$desc = $pquery->item(0)->nodeValue;
				$suff = mb_strlen($desc) > $length ? "..." : "";
				$desc = mb_substr($desc, 0, $length) . $suff;
			}

			if ($iquery->length) {
				$img = $iquery->item(0)->getAttribute("src");
			}

			$imgs[$entity->id] = $img ?? null;
			$descs[$entity->id] = $desc ?? null;
			unset($img, $desc);
		}

		return [ $imgs ?? [], $descs ?? [] ];
	}

}

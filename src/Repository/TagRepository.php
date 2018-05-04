<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TagRepository extends ServiceEntityRepository {

	function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Tag::class);
	}

	/**
	 * Parses tags string
	 * @param string $tag_string string of tags, separated by `,`
	 * @return array
	 */
	function tags($tag_string) {
		$tag_string = preg_replace("/((?![\w,]).)+/", "", $tag_string);
		$tag_string = mb_strtolower($tag_string);
		return explode(",", $tag_string);
	}

	/**
	 * Creates tags in db if they not exists
	 * @param string $tag_string string of tags, separated by `,`
	 * @param int $limit limit of tags to add/return as result
	 * @return array
	 */
	function add_tags($tag_string, $limit = 5) {
		$tags = $this->tags($tag_string);
		$entities = $this->get_tags($tag_string, $limit);
		$em = $this->getEntityManager();

		if (count($entities) < $limit) {
			$exists = array_column($entities, "name");
			$nexists = array_diff($tags, $exists);
			$nexists = array_slice($nexists, count($exists), $limit);

			foreach ($nexists as $name) {
				$entity = new Tag;
				$entity->name = $name;
				$entities[] = $entity;
				$em->persist($entity);
			}

			$em->flush();
		}

		return $entities;
	}

	/**
	 * Gets tags from db by tag string
	 * @param string $tag_string string of tags, separated by `,`
	 * @param int $limit limit of tags to load
	 * @param int $offset offset count
	 * @return array
	 */
	function get_tags($tag_string, $limit = 5, $offset = 0) {
		$tags = $this->tags($tag_string);

		$query = $this
			->createQueryBuilder("t")
			->where("t.name IN (:tags)")
			->setFirstResult($offset)
			->setMaxResults($limit)
			->setParameter("tags", $tags)
			->getQuery();

		return $query->execute();
	}

}

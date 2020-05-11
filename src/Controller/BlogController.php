<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManagerInterface as EM;

class BlogController extends Controller
{
    /**
     * Renders articles list page
     *
     * @param string $category name
     * @param int $page for pagination, >0
     */
    public function list(EM $em, $category, int $page)
    {
        [ $limit, $plimit ] = [ 6, 5 ];
        $offset = ($page-1) * $limit;

        $repo = $em->getRepository(Article::class);
        $entities = $repo->list($category, $limit, $offset);
        [ $img, $desc ] = $repo->description($entities);

        $pcount = ceil($repo->count($category) / $limit);
        $pcount = $pcount > $plimit ? $plimit : $pcount;

        if (!count($entities)) {
            throw new NotFoundHttpException("Articles not found");
        }

        return $this->render("list.html.twig", compact(
            "category",
            "entities",
            "img",
            "desc",
            "page",
            "pcount"
        ));
    }
}

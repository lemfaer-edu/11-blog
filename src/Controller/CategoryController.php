<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface as EM;

class CategoryController extends Controller
{
    /**
     * Creates or updates category
     *
     * @param int|null $id category id
     */
    public function save(EM $em, int $id = null)
    {
        $entity = $id ? $em->find(Category::class, $id) : new Category;

        if (false === $this->isGranted("ROLE_ADMIN")) {
            throw new AccessDeniedException("Not allowed");
        }

        if (is_null($entity)) {
            throw new NotFoundHttpException("Article not found");
        }

        return $this->render("category.html.twig", compact("entity"));
    }

    /**
     * Creates / Updates category in db
     *
     * @param int|null $id category id
     */
    public function save_submit(EM $em, Request $request, int $id = null)
    {
        $csrf_id = "category-save";
        $csrf_token = $request->get("csrf_token");
        $csrf_valid = $this->isCsrfTokenValid($csrf_id, $csrf_token);
        $entity = $id ? $em->find(Category::class, $id) : new Category;

        if (!$csrf_valid) {
            throw new AccessDeniedException("Wrong csrf token");
        }

        if (false === $this->isGranted("ROLE_ADMIN")) {
            throw new AccessDeniedException("Not allowed");
        }

        if (is_null($entity)) {
            throw new NotFoundHttpException("Article not found");
        }

        $entity->name = $request->get("name");
        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute("blog_list");
    }
}

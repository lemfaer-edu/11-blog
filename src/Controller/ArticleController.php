<?php

namespace App\Controller;

use App\Entity\{ Article, Category, Tag };
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface as EM;

class ArticleController extends Controller
{
    /**
     * Renders article content by id
     *
     * @param int $id article id
     */
    public function view(EM $em, int $id)
    {
        $entity = $em->find(Article::class, $id);
        if (is_null($entity)) {
            throw new NotFoundHttpException("Article not found");
        }

        return $this->render("article/view.html.twig", compact("entity"));
    }

    /**
     * Renders page with article edit form
     *
     * @param int|null $id article id to edit
     */
    public function write(EM $em, int $id = null)
    {
        $entity = $id ? $em->find(Article::class, $id) : new Article;

        if (false === $this->isGranted("IS_AUTHENTICATED_FULLY")) {
            throw new AccessDeniedException("Not authenticated");
        }

        if (is_null($entity)) {
            throw new NotFoundHttpException("Article not found");
        }

        if ($id && $this->getUser()->id !== $entity->author->id) {
            throw new AccessDeniedException("Not allowed");
        }

        $categories = $em->getRepository(Category::class)->findAll();
        $tags = $entity->tags ? $entity->tags->getValues() : [];
        $tags = $em->getRepository(Tag::class)->tag_string($tags);

        return $this->render("article/form.html.twig", compact("entity", "categories", "tags"));
    }

    /**
     * Performs actions to create/edit article
     *
     * @param string <title> title
     * @param int <category> category id
     * @param string <content> content in html format
     * @param string <tags> string of tags, separated by `,`
     * @param string <csrf_token> token
     * @param int|null $id article id to edit
     */
    public function write_submit(EM $em, Request $request, int $id = null)
    {
        $csrf_id = "article-save";
        $csrf_token = $request->get("csrf_token");
        $csrf_valid = $this->isCsrfTokenValid($csrf_id, $csrf_token);
        $entity = $id ? $em->find(Article::class, $id) : new Article;

        if (!$csrf_valid) {
            throw new AccessDeniedException("Wrong csrf token");
        }

        if (false === $this->isGranted("IS_AUTHENTICATED_FULLY")) {
            throw new AccessDeniedException("Not authenticated");
        }

        if (is_null($entity)) {
            throw new NotFoundHttpException("Article not found");
        }

        if ($id && $this->getUser()->id !== $entity->author->id) {
            throw new AccessDeniedException("Not allowed");
        }

        $tag_repo = $em->getRepository(Tag::class);
        $tags = $tag_repo->add_tags($request->get("tags"));

        $entity->category = $em->find(Category::class, $request->get("category"));
        $entity->content = $request->get("content");
        $entity->title = $request->get("title");
        $entity->author = $this->getUser();
        $entity->tags = $tags;

        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute("article_view", [ "id" => $entity->id ]);
    }
}

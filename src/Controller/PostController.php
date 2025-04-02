<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Rubrik;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\PostRepository;
use App\Service\BreadcrumbService;
use App\Repository\RubrikRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PostController extends AbstractController


{
    private $repo;
    private $emi;
    private $breadcrumbService;

    public function __construct(PostRepository $repo, EntityManagerInterface $emi, BreadcrumbService $breadcrumbService)
    {
        $this->repo = $repo;
        $this->emi = $emi;
        $this->breadcrumbService = $breadcrumbService;
    }

    // Gestion de l'affichage de la page d'accueil (index)
    #[Route('/', name: 'app_post')]
    public function index(): Response
    {
        $posts = $this->repo->findBy([], ['createdAt' => 'DESC'], 4);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    //GESTION DE LA RECUPERATION DU DETAIL D'UN POST(TOTALITE D'UN POST)+Commentaires

    #[Route('/post/{id}/{slug}', name: 'show', requirements: ['id' => '\d+'])]
    public function showone(int $id, string $slug, Post $post, Request $req, CommentRepository $crepo): Response
    {
        // Ajout des breadcrumbs
        $this->breadcrumbService->addBreadcrumb('Accueil', $this->generateUrl('app_post'));

        //Si j'ai un objet Rubrik lié au post
        $rubrik = $post->getRubrik(); // Si vous avez un lien entre Post et Rubrik
        if ($rubrik) {
            $this->breadcrumbService->addBreadcrumb($rubrik->getName(), $this->generateUrl('posts_by_rubrik', ['id' => $rubrik->getId()]));
        }

        // Breadcrumb pour l'article actuel
        $this->breadcrumbService->addBreadcrumb($post->getTitle(), $this->generateUrl('show', [
            'id' => $post->getId(),
            'slug' => $post->getSlug()
        ]));

        // // Création et gestion du formulaire de commentaire
        // $comment = new Comment();
        // $commentForm = $this->createForm(CommentFormType::class, $comment);
        // $commentForm->handleRequest($req);

        // if ($commentForm->isSubmitted()) {
        //     if ($commentForm->isValid()) {
        //         // Vérifier si l'utilisateur est connecté
        //         if (!$this->getUser()) {
        //             $this->addFlash('warning', 'Vous devez être connecté pour laisser un commentaire.');
        //             return $this->redirectToRoute('app_login');
        //         }

        //         // Persister le commentaire
        //         $comment->setUser($this->getUser());
        //         $comment->setPost($post);
        //         $comment->setCreatedAt(new \DateTimeImmutable());

        //         try {
        //             $this->emi->persist($comment);
        //             $this->emi->flush();
        //             // Rediriger pour éviter la resoumission du formulaire
        //             return $this->redirectToRoute('show', [
        //                 'id' => $post->getId(),
        //                 'slug' => $post->getSlug()
        //             ]);
        //         } catch (\Exception $e) {
        //             $this->addFlash('danger', 'Erreur lors de l\'envoi du commentaire.');
        //             dump($e->getMessage()); // Pour debug
        //         }
        //     } else {
        //         // Affichage des erreurs en cas d'invalidité
        //         dump($commentForm->getErrors(true));
        //     }
        // }

        // // Récupération des commentaires pour affichage
        // $allComments = $crepo->findByPostOrderedByCreatedAtDesc($post->getId());

        // Retourner la vue avec les données
        return $this->render('show/show.html.twig', [
            'post' => $post,
            // 'comments' => $allComments,
            // 'comment_form' => $commentForm->createView(),
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs(),
        ]);
    }
    //Gestion de l'affichage des rubriques
    #[Route('/rubrik/rubrik/{id}', name: 'posts_by_rubrik')]
    public function postsByRubrik(Rubrik $rubrik, Request $req, PostRepository $postRepository): Response
    {
        // Gestion de la pagination
        $page = max(1, $req->query->getInt('page', 1));
        $limit = 6;

        // Ajouter le filtre de la rubrique
        $filters = ['rubrik' => $rubrik];
        $paginator = $postRepository->findFilteredPaginatedPosts($filters, $page, $limit);

        // Récupérer le nombre total d'articles correspondant aux filtres
        $totalPosts = $postRepository->countFilteredPosts($filters);
        $totalPages = ceil($totalPosts / $limit); // Nombre total de pages

        // Ajout des breadcrumbs
        $this->breadcrumbService->addBreadcrumb('Accueil', $this->generateUrl('app_post'));
        $this->breadcrumbService->addBreadcrumb($rubrik->getName(), $this->generateUrl('posts_by_rubrik', ['id' => $rubrik->getId()]));

        return $this->render('rubrik/rubrik.html.twig', [
            'rubrik' => $rubrik,
            'posts' => $paginator, // Utilisation directe de la pagination
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs(),
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
}

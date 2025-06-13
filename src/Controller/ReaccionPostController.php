<?php

namespace App\Controller;

use App\Entity\ReaccionPost;
use App\Form\ReaccionPostType;
use App\Repository\ReaccionPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reaccion/post')]
class ReaccionPostController extends AbstractController
{
    #[Route('/', name: 'app_reaccion_post_index', methods: ['GET'])]
    public function index(ReaccionPostRepository $reaccionPostRepository): Response
    {
        return $this->render('reaccion_post/index.html.twig', [
            'reaccion_posts' => $reaccionPostRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reaccion_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reaccionPost = new ReaccionPost();
        $form = $this->createForm(ReaccionPostType::class, $reaccionPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reaccionPost);
            $entityManager->flush();

            return $this->redirectToRoute('app_reaccion_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reaccion_post/new.html.twig', [
            'reaccion_post' => $reaccionPost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reaccion_post_show', methods: ['GET'])]
    public function show(ReaccionPost $reaccionPost): Response
    {
        return $this->render('reaccion_post/show.html.twig', [
            'reaccion_post' => $reaccionPost,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reaccion_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReaccionPost $reaccionPost, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReaccionPostType::class, $reaccionPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reaccion_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reaccion_post/edit.html.twig', [
            'reaccion_post' => $reaccionPost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reaccion_post_delete', methods: ['POST'])]
    public function delete(Request $request, ReaccionPost $reaccionPost, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reaccionPost->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reaccionPost);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reaccion_post_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\ReaccionComentario;
use App\Form\ReaccionComentarioType;
use App\Repository\ReaccionComentarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reaccion/comentario')]
class ReaccionComentarioController extends AbstractController
{
    #[Route('/', name: 'app_reaccion_comentario_index', methods: ['GET'])]
    public function index(ReaccionComentarioRepository $reaccionComentarioRepository): Response
    {
        return $this->render('reaccion_comentario/index.html.twig', [
            'reaccion_comentarios' => $reaccionComentarioRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reaccion_comentario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reaccionComentario = new ReaccionComentario();
        $form = $this->createForm(ReaccionComentarioType::class, $reaccionComentario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reaccionComentario);
            $entityManager->flush();

            return $this->redirectToRoute('app_reaccion_comentario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reaccion_comentario/new.html.twig', [
            'reaccion_comentario' => $reaccionComentario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reaccion_comentario_show', methods: ['GET'])]
    public function show(ReaccionComentario $reaccionComentario): Response
    {
        return $this->render('reaccion_comentario/show.html.twig', [
            'reaccion_comentario' => $reaccionComentario,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reaccion_comentario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReaccionComentario $reaccionComentario, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReaccionComentarioType::class, $reaccionComentario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reaccion_comentario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reaccion_comentario/edit.html.twig', [
            'reaccion_comentario' => $reaccionComentario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reaccion_comentario_delete', methods: ['POST'])]
    public function delete(Request $request, ReaccionComentario $reaccionComentario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reaccionComentario->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reaccionComentario);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reaccion_comentario_index', [], Response::HTTP_SEE_OTHER);
    }
}

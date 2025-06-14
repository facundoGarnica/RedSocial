<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Comentario;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        $user = $this->getUser();
        $posts = [];

        if ($user) {
            // Traer TODOS los posts con usuarios y comentarios, sin filtrar por usuario
            $posts = $postRepository->findAllWithUsersAndComments();
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts,
        ]);
    }

    #[Route('/comentarPost/{id}', name: 'app_comentar_post', methods: ['POST'])]
    public function comentarPost(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $descripcion = trim($request->request->get('descripcion'));

        if ($descripcion) {
            $comentario = new Comentario();
            $comentario->setDescripcion($descripcion);
            $comentario->setUsuario($user);
            $comentario->setPost($post);
            $comentario->setFechaCreacion(new \DateTime());
            $em->persist($comentario);
            $em->flush();

            $this->addFlash('success', 'Comentario creado correctamente.');
        } else {
            $this->addFlash('error', 'El comentario no puede estar vacío.');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/comentario/{id}/delete', name: 'comentario_delete', methods: ['POST'])]
    public function deleteComentario(Comentario $comentario, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Verificamos que el usuario sea el autor del comentario
        if ($comentario->getUsuario()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('No tienes permiso para eliminar este comentario.');
        }

        if ($this->isCsrfTokenValid('delete_comentario' . $comentario->getId(), $request->request->get('_token'))) {
            $em->remove($comentario);
            $em->flush();

            $this->addFlash('success', 'Comentario eliminado correctamente.');
        } else {
            $this->addFlash('error', 'Token CSRF inválido.');
        }

        return $this->redirectToRoute('app_home');
    }
}

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
use App\Entity\ReaccionPost;
use App\Repository\ReaccionPostRepository;
use App\Entity\ReaccionComentario;
use App\Repository\ReaccionComentarioRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
class HomeController extends AbstractController
{
   #[Route('/home', name: 'app_home')]
    public function index(PostRepository $postRepository, ReaccionComentarioRepository $reaccionComentarioRepo): Response
    {
        $user = $this->getUser();
        $posts = [];
        $reaccionesComentarios = [];
        $reaccionesPosts = [];

        if ($user) {
            $posts = $postRepository->findAllWithUsersAndComments();

            // Reacciones por comentario
            $comentarios = array_merge(...array_map(fn($p) => $p->getComentarios()->toArray(), $posts));
            foreach ($comentarios as $comentario) {
                $id = $comentario->getId();
                $reaccionesComentarios[$id] = [];

                foreach ($comentario->getReaccionComentarios() as $reaccion) {
                    $tipo = $reaccion->getEmoticon();
                    $reaccionesComentarios[$id][$tipo] = ($reaccionesComentarios[$id][$tipo] ?? 0) + 1;
                }
            }

            // Reacciones por post
            foreach ($posts as $post) {
                $reaccionesPosts[$post->getId()] = [];

                foreach ($post->getReaccionPosts() as $reaccion) {
                    $tipo = $reaccion->getEmoticon();
                    $reaccionesPosts[$post->getId()][$tipo] = ($reaccionesPosts[$post->getId()][$tipo] ?? 0) + 1;
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'reaccionesComentarios' => $reaccionesComentarios,
            'reaccionesPosts' => $reaccionesPosts, // 👈 importante
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

    #[Route('reaccioncomentario/{id}', name: 'app_reaccionar_comentario', methods: ['POST'])]
    public function reaccionarComentario(Request $request, Comentario $comentario, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $reaccioncomentario = new ReaccionComentario();

        $reaccioncomentario->setUsuario($user);
        $reaccioncomentario->setComentario($comentario);

        $emoticon = $request->request->get('reaction');

        if (!$emoticon) {
            $this->addFlash('error', 'No se recibió ninguna reacción.');
            return $this->redirectToRoute('app_home');
        }

        // Verificar si ya hay una reacción de este usuario para este comentario
        $reaccionExistente = $em->getRepository(ReaccionComentario::class)
            ->findOneBy(['usuario' => $user, 'comentario' => $comentario]);

        if ($reaccionExistente) {
            // Actualizamos la reacción existente
            $reaccionExistente->setEmoticon($emoticon);
            $this->addFlash('success', 'Tu reacción fue actualizada.');
        } else {
            // Creamos una nueva
            $reaccioncomentario->setEmoticon($emoticon);
            $em->persist($reaccioncomentario);
            $this->addFlash('success', 'Reacción añadida correctamente.');
        }

        $em->flush();

        return $this->redirectToRoute('app_home');
    }

    
    #[Route('reaccionarpost/{id}', name: 'app_reaccionar_post', methods: ['POST'])]
    public function reaccionarPost(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $emoticon = $request->request->get('reaction');

        if (!$emoticon) {
            $this->addFlash('error', 'No se recibió ninguna reacción.');
            return $this->redirectToRoute('app_home');
        }

        // Verificar si ya hay una reacción de este usuario para este post
        $reaccionExistente = $em->getRepository(ReaccionPost::class)
            ->findOneBy(['usuario' => $user, 'post' => $post]);

        if ($reaccionExistente) {
            // Actualizamos la reacción existente
            $reaccionExistente->setEmoticon($emoticon);
            $this->addFlash('success', 'Tu reacción fue actualizada.');
        } else {
            // Creamos una nueva
            $reaccionpost = new ReaccionPost();
            $reaccionpost->setUsuario($user);
            $reaccionpost->setPost($post);
            $reaccionpost->setEmoticon($emoticon);
            $em->persist($reaccionpost);
            $this->addFlash('success', 'Reacción añadida correctamente.');
        }

        $em->flush();

        return $this->redirectToRoute('app_home');
    }
}
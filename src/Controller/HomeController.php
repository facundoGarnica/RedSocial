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

   #[Route('borrarpost/{id}', name: 'app_borrar_post', methods: ['POST'])]
public function borrarPost(Post $post, Request $request, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    if ($post->getUsuario()->getId() !== $user->getId() && !in_array('ROLE_ADMIN', $user->getRoles())) {
        throw $this->createAccessDeniedException('No tienes permiso para eliminar este post.');
    }

    if (!$this->isCsrfTokenValid('delete_post' . $post->getId(), $request->request->get('_token'))) {
        $this->addFlash('error', 'Token CSRF inválido.');
        return $this->redirectToRoute('app_home');
    }

    // Eliminar imagen física si existe
    if ($post->getImagen()) {
        $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/images/' . $post->getImagen();
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    }

    $em->remove($post);
    $em->flush();

    if ($request->isXmlHttpRequest()) {
        return new JsonResponse(['message' => 'Post eliminado correctamente.']);
    }

    $this->addFlash('success', 'Post eliminado correctamente.');
    return $this->redirectToRoute('app_home');
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

        // Verificamos que el usuario sea el autor del comentario o que tenga el rol de administrador
        if ($comentario->getUsuario()->getId() !== $user->getId() && !in_array('ROLE_ADMIN', $user->getRoles())) {
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
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'No autenticado'], 401);
            }
            return $this->redirectToRoute('app_login');
        }

        $emoticon = $request->request->get('reaction');

        if (!$emoticon) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'No se recibió ninguna reacción.'], 400);
            }
            $this->addFlash('error', 'No se recibió ninguna reacción.');
            return $this->redirectToRoute('app_home');
        }

        $repo = $em->getRepository(ReaccionComentario::class);
        $reaccionExistente = $repo->findOneBy(['usuario' => $user, 'comentario' => $comentario]);

        if ($reaccionExistente) {
            $reaccionExistente->setEmoticon($emoticon);
            $mensaje = 'Tu reacción fue actualizada.';
        } else {
            $reaccionComentario = new ReaccionComentario();
            $reaccionComentario->setUsuario($user);
            $reaccionComentario->setComentario($comentario);
            $reaccionComentario->setEmoticon($emoticon);
            $em->persist($reaccionComentario);
            $mensaje = 'Reacción añadida correctamente.';
        }

        $em->flush();

        // Inicializar contadores para cada tipo de reacción
        $reacciones = [
            'me_gusta' => 0,
            'no_me_gusta' => 0,
            'me_entristece' => 0,
            'me_enoja' => 0,
            'me_encanta' => 0,
        ];

        // Contar reacciones actuales del comentario
        foreach ($comentario->getReaccionComentarios() as $r) {
            $tipo = $r->getEmoticon();
            if (isset($reacciones[$tipo])) {
                $reacciones[$tipo]++;
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => $mensaje,
                'reacciones' => $reacciones,
            ]);
        }

        $this->addFlash('success', $mensaje);
        return $this->redirectToRoute('app_home');
    }

    
    #[Route('reaccionarpost/{id}', name: 'app_reaccionar_post', methods: ['POST'])]
    public function reaccionarPost(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'No autenticado'], 401);
            }
            return $this->redirectToRoute('app_login');
        }

        $emoticon = $request->request->get('reaction');

        if (!$emoticon) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'No se recibió ninguna reacción.'], 400);
            }
            $this->addFlash('error', 'No se recibió ninguna reacción.');
            return $this->redirectToRoute('app_home');
        }

        $repo = $em->getRepository(ReaccionPost::class);
        $reaccionExistente = $repo->findOneBy(['usuario' => $user, 'post' => $post]);

        if ($reaccionExistente) {
            $reaccionExistente->setEmoticon($emoticon);
            $mensaje = 'Tu reacción fue actualizada.';
        } else {
            $reaccionpost = new ReaccionPost();
            $reaccionpost->setUsuario($user);
            $reaccionpost->setPost($post);
            $reaccionpost->setEmoticon($emoticon);
            $em->persist($reaccionpost);
            $mensaje = 'Reacción añadida correctamente.';
        }

        $em->flush();

        // Contadores de reacciones actualizados
        $reacciones = [
            'me_gusta' => 0,
            'no_me_gusta' => 0,
            'me_entristece' => 0,
            'me_enoja' => 0,
            'me_encanta' => 0,
        ];

        foreach ($post->getReaccionPosts() as $r) {
            $tipo = $r->getEmoticon();
            if (isset($reacciones[$tipo])) {
                $reacciones[$tipo]++;
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => $mensaje,
                'reacciones' => $reacciones,
            ]);
        }

        $this->addFlash('success', $mensaje);
        return $this->redirectToRoute('app_home');
    }
}
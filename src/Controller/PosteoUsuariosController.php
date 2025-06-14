<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PosteoUsuariosController extends AbstractController
{
    #[Route('/posteo/usuarios', name: 'app_posteo_usuarios')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PostRepository $postRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $post->setUsuario($user);
        $post->setFechaCreacion(new \DateTime());

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagen = $form['imagen']->getData();
            if ($imagen) {
                $nombreArchivo = uniqid().'.'.$imagen->guessExtension();
                $imagen->move($this->getParameter('images_directory'), $nombreArchivo);
                $post->setImagen($nombreArchivo);
            }

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post creado con éxito.');

            return $this->redirectToRoute('app_posteo_usuarios');
        }

        $posts = $postRepository->findBy(['usuario' => $user], ['FechaCreacion' => 'DESC']);

        return $this->render('posteo_usuarios/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts,
        ]);
    }

    #[Route('/posteo/usuarios/delete/{id}', name: 'app_posteo_usuarios_delete', methods: ['POST'])]
public function delete(
    Request $request,
    Post $post,
    EntityManagerInterface $em
): Response {
    $user = $this->getUser();

    if (!$user || $post->getUsuario() !== $user) {
        $this->addFlash('error', 'No tenés permiso para eliminar este post.');
        return $this->redirectToRoute('app_posteo_usuarios');
    }

    if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
        // Ruta absoluta de la carpeta donde guardás las imágenes
        $imagesDir = $this->getParameter('images_directory');

        // Borrar la imagen física si existe
        if ($post->getImagen()) {
            $imagenPath = $imagesDir . '/' . $post->getImagen();
            if (file_exists($imagenPath)) {
                unlink($imagenPath);
            }
        }

        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Post eliminado correctamente.');
    } else {
        $this->addFlash('error', 'Token CSRF inválido.');
    }

    return $this->redirectToRoute('app_posteo_usuarios');
}

}

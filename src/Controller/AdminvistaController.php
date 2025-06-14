<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\PostRepository;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdminvistaController extends AbstractController
{
    #[Route('/adminvista', name: 'app_adminvista')]
    public function index(UserRepository $userRepository, PostRepository $postRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('adminvista/index.html.twig', [
            'usuarios' => $userRepository->findAll(),
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/admin/post/{id}/estado', name: 'admin_actualizar_estado_post', methods: ['POST'])]
    public function actualizarEstado(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $estado = $request->request->get('estado');

        if (!in_array($estado, ['pendiente', 'aprobado', 'no_aprobado'])) {
            $this->addFlash('error', 'Estado inválido');
            return $this->redirectToRoute('app_adminvista');
        }

        $post->setEstado($estado);
        $em->flush();

        $this->addFlash('success', 'Estado actualizado correctamente.');
        return $this->redirectToRoute('app_adminvista');
    }

   #[Route('/admin/usuarios/{id}/roles', name: 'admin_actualizar_roles_usuario', methods: ['POST'])]
public function actualizarRolesUsuario(int $id, Request $request, EntityManagerInterface $em): Response
{
    $usuario = $em->getRepository(User::class)->find($id);
    if (!$usuario) {
        throw $this->createNotFoundException('Usuario no encontrado');
    }

    // Obtener roles del formulario (array), con valor por defecto para evitar null
    $roles = $request->request->all('roles');
if (!is_array($roles)) {
    $roles = [];
}

    // Validar roles permitidos
    $rolesPermitidos = ['ROLE_USER', 'ROLE_ADMIN'];

    $rolesFiltrados = array_filter($roles, fn($rol) => in_array($rol, $rolesPermitidos));

    // Asegurar al menos ROLE_USER
    if (!in_array('ROLE_USER', $rolesFiltrados)) {
        $rolesFiltrados[] = 'ROLE_USER';
    }

    $usuario->setRoles($rolesFiltrados);

    $em->persist($usuario);
    $em->flush();

    $this->addFlash('success', 'Roles actualizados correctamente.');

    // Redirigir a la página del panel administrativo (ajusta la ruta)
    return $this->redirectToRoute('app_adminvista');
}
#[Route('/admin/post/{id}/eliminar', name: 'admin_eliminar_post', methods: ['POST'])]
public function eliminarPost(
    int $id,
    EntityManagerInterface $em,
    Request $request,
    ParameterBagInterface $params
): Response {
    $post = $em->getRepository(Post::class)->find($id);

    if (!$post) {
        throw $this->createNotFoundException('Post no encontrado');
    }

    if (!$this->isCsrfTokenValid('eliminar_post_' . $post->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF inválido');
    }

    // Obtener la ruta absoluta del proyecto
    $projectDir = $params->get('kernel.project_dir');

    // Eliminar imagen si existe
    if ($post->getImagen()) {
        $rutaImagen = $projectDir . '/public/images/' . $post->getImagen();
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    }

    $em->remove($post);
    $em->flush();

    $this->addFlash('success', 'Post eliminado correctamente.');

    return $this->redirectToRoute('app_adminvista');
}
#[Route('/admin/usuario/{id}/eliminar', name: 'admin_eliminar_usuario', methods: ['POST'])]
public function eliminarUsuario(
    int $id,
    EntityManagerInterface $em,
    Request $request
): Response {
    $usuario = $em->getRepository(User::class)->find($id);

    if (!$usuario) {
        throw $this->createNotFoundException('Usuario no encontrado');
    }

    if (!$this->isCsrfTokenValid('eliminar_usuario_' . $usuario->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF inválido');
    }

    $em->remove($usuario);
    $em->flush();

    $this->addFlash('success', 'Usuario eliminado correctamente.');

    return $this->redirectToRoute('app_adminvista');
}
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
class ConfiguracionController extends AbstractController
{
    #[Route('/configuracion', name: 'app_configuracion')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('configuracion/index.html.twig', [
            'controller_name' => 'ConfiguracionController',
            'user' => $user,
        ]);
    }
}

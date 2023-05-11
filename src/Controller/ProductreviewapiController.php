<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductreviewapiController extends AbstractController
{
    #[Route('/productreviewapi', name: 'app_productreviewapi')]
    public function index(): Response
    {
        return $this->render('productreviewapi/index.html.twig', [
            'controller_name' => 'ProductreviewapiController',
        ]);
    }
}

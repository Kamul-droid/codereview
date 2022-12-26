<?php

namespace App\Controller;

use App\Repository\ExerciceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="app_index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'user' =>  $this->getUser(),
        ]);
    }
    /**
     * @Route("/exercices", name="app_exo")
     */
    public function exo( ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('index/exercice.html.twig', [
            'user' =>  $this->getUser(),
            'exercices' => $exerciceRepository->findAll(),
        ]);
    }
}

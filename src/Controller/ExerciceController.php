<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/exercice")
 */
class ExerciceController extends AbstractController
{
    /**
     * @Route("/", name="app_exercice_index", methods={"GET"})
     */
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        
        return $this->render('index/exercice.html.twig', [
            'exercices' => $exerciceRepository->findAll(),
            'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/new", name="app_exercice_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ExerciceRepository $exerciceRepository, ImageRepository $imageRepository): Response
    {
        $exercice = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('picture')->getData();

            // on boucle sur les images
            foreach($images as $img){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$img->guessExtension();

                // On copie le fichier dans le dossier indiqué
                //move_uploaded_file($fichier,'images_directory');
                $img->move($this->getParameter('images_directory'),$fichier);
                
                // On stocke l´image dans la base de données Articles
                $NewImage = new Image();
                $NewImage -> setName($fichier);
                $NewImage->setExo($exercice);
                $imageRepository->add($NewImage,true);
                
                $exercice   -> addImage($NewImage);
               

            
            }
            $exerciceRepository->add($exercice);
            return $this->redirectToRoute('app_exercice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice/new.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
            'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_exercice_show", methods={"GET"})
     */
    public function show(Exercice $exercice): Response
    {
        return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
            'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_exercice_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Exercice $exercice, ExerciceRepository $exerciceRepository): Response
    {
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exerciceRepository->add($exercice);
            return $this->redirectToRoute('app_exercice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice/edit.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
            'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_exercice_delete", methods={"POST"})
     */
    public function delete(Request $request, Exercice $exercice, ExerciceRepository $exerciceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exercice->getId(), $request->request->get('_token'))) {
            $exerciceRepository->remove($exercice);
        }

        return $this->redirectToRoute('app_exercice_index', [], Response::HTTP_SEE_OTHER);
    }
}

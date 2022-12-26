<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\File;
use App\Form\FileExoType;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use SysvSemaphore;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * @Route("/file")
 */
class FileController extends AbstractController
{
    /**
     * @Route("/", name="app_file_index", methods={"GET"})
     */
    public function index(FileRepository $fileRepository): Response
    {
        return $this->render('file/index.html.twig', [
            'files' => $fileRepository->findAll(),'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/new", name="app_file_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FileRepository $fileRepository,SessionInterface $session): Response
    {   
        $store = new FlockStore();
        $factory = new Factory($store);
        $lockreg = $factory->createLock('reg');

        $file = new File();
        $form = $this->createForm(FileExoType::class, $file);
        $form->handleRequest($request);
        $id = (int)$request->query->get('id');
        if ($id) {
           
            $session->set('id',$id);
        }
        // dump($id);
        if ($form->isSubmitted() && $form->isValid()) {
             //Process uploadFile
             $uploadFile = $form->get('name')->getData();
             $newFilename="";
             $file_extension = pathinfo($uploadFile->getClientOriginalName(),PATHINFO_EXTENSION);
             if ( strval($file_extension) == "c" || strval($file_extension) == "py") {
                
                 // this condition is needed because the 'uploadFile' field is not required
                // so the file must be processed only when a file is uploaded
             if ($uploadFile) {
                  $originalFilename = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_FILENAME);
                  // this is needed to safely include the file name as part of the URL
                //  $safeFilename = $slugger->slug($originalFilename);
                  $newFilename = $originalFilename.'-'.uniqid().'.'.$file_extension;
 
                  // Move the file to the directory where uploadFiles are stored
                  try {
                        $uploadFile->move(
                            $this->getParameter('exo_directory'),
                            $newFilename
                            );
                           
                            // lock the resource until available
                            if ($lockreg->acquire()) {
                                $this->addFlash(
                                   'success',
                                   'Votre fichier est ajouté 
                                   à la file d\'attente'
                                );
                                try {
                                    $fp = fopen('D:/codereview/public/exo/upload_ex_list.txt', 'a')or die("Unable to open file!");
                                    // we will write reference to the exercice_id, submit file and user_id
                                    fwrite($fp, $newFilename.'_'.$this->getUser()->getId()."\n");
                                    fclose($fp);
                                   
                                } finally {
                                    $lockreg->release();
                                }
                               
                               
                            } else {
                                $this->addFlash(
                                   'info',
                                   'En attente ...'
                                );
                            }
                            
                     } catch (FileException $e) {
                      // ... handle exception if something happens during file upload
                    }
 
                    // updates the 'uploadFilename' property to store the PDF file name
                    // instead of its contents
                    
                    $file -> setName($newFilename);
                    $file->setLangage($file_extension);
                    $file->setUser($this->getUser());
                }
                    $fileRepository->add($file);
                    $id = $session->get('id');
                    return $this->redirectToRoute('app_wait_index', ['id'=>$id,
                    'filex'=>$file_extension,
                    'fileName'=>$newFilename], Response::HTTP_SEE_OTHER);
            
            } else {
               
                $this->addFlash(
                   'danger',
                   'Vous avez soumis un fichier terminant par : '.$file_extension.', Vous devez uploader un fichier C ou Python ',
                );
                return $this->redirectToRoute('app_file_new');
             }

            
 
           
        }

        return $this->render('file/new.html.twig', [
            'file' => $file,
            'form' => $form->createView(),'user' =>  $this->getUser(),
            
        ]);
    }

    /**
     * @Route("/{id}", name="app_file_show", methods={"GET"})
     */
    public function show(File $file): Response
    {
        return $this->render('file/show.html.twig', [
            'file' => $file,'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_file_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, File $file, FileRepository $fileRepository): Response
    {
        $form = $this->createForm(FileExoType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileRepository->add($file);
            return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file/edit.html.twig', [
            'file' => $file,
            'form' => $form->createView(),'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_file_delete", methods={"POST"})
     */
    public function delete(Request $request, File $file, FileRepository $fileRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $fileRepository->remove($file);
        }

        return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Form\MarkType;
use DateTimeImmutable;
use App\Entity\Exercice;
use App\Repository\MarkRepository;
use Symfony\Component\Lock\Factory;
use App\Repository\ExerciceRepository;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Process\Process;

/**
 * @Route("/mark")
 */
class MarkController extends AbstractController
{
    private int $ok =1;

    /**
     * @Route("/", name="app_mark_index", methods={"GET"})
     */
    public function index(MarkRepository $markRepository): Response
    {
       
        return $this->render('mark/index.html.twig', [
            'marks' => $markRepository->findBy(['user'=> $this->getUser() ],['score'=>'desc']) ,'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/wait", name="app_wait_index", methods={"GET"})
     */
    public function wait_f( ExerciceRepository $ex,MarkRepository $markRepository, Request $request): Response
    {

        $store = new FlockStore();
        $factory = new LockFactory($store);
        $lockcode = $factory->createLock('code');
        $state ="start";
        
        $id = (int)$request->query->get('id');
        $filext = $request->query->get('filex');
        $fileNam = $request->query->get('fileName');
        $exo = $ex->findOneBy(['id'=>$id]);
        dump($lockcode->acquire());
        if (!$lockcode->acquire()) {
            
            return $this->redirectToRoute('app_wait_inde',[
                'user' =>  $this->getUser(),
                'state'=>$state,
            ]);
        }
        
        if ($lockcode->acquire()) {
            
            try {
                // perform a job during less than 30 seconds
                
                
               
                    # code...
                    $this->addFlash(
                       'notice',
                       'La correction de votre code a commencé '
                    );
                    
                    if ($filext == 'py') {
                        $note = 0;
                        // Exercice valeur absolue
                        $process = new Process(['python', $this->getParameter('code_directory').'\\'.$fileNam, 5]);
                        $process->run();
                        // dump($this->getParameter('code_directory').'\\'.$fileNam);
                        $res = $process->getOutput();
                        $preRes = trim(preg_replace('/\s\s+/', ' ', $res));
                        // dump($res);
                        // dump($preRes);
                        $expectedValue1  = 10;
                        if ($preRes == $expectedValue1) {
                            $note+=50;
                        }
                        // second test
                        $process = new Process(['python', $this->getParameter('code_directory').'\\'.$fileNam, -5]);
                        $process->run();
                        // dump($this->getParameter('code_directory').'\\'.$fileNam);
                        $res = $process->getOutput();
                        $preRes = trim(preg_replace('/\s\s+/', ' ', $res));
                        // dump($res);
                        // dump($preRes);
                        $expectedValue1  = -10;
                        if ($preRes == $expectedValue1) {
                            $note+=50;
                        }

                        // check if note is already assign to the exo then update
                        $isMarkExist = $markRepository->findOneBy(['exo'=>$exo->getId(),'user'=> $this->getUser()]);
                        if ($isMarkExist != null) {
                            # code...
                            // get new score 
                            $newScore = $note;
                            $isMarkExist->setScore($newScore);
                            $markRepository->add($isMarkExist);
                        } else {
                            
                            $mark = new Mark();
                            $mark->setUser($this->getUser());
                            $mark->setExo($exo);
                            $mark->setStartedAt(new DateTimeImmutable('now'));
                            // get mark 
                            $score = $note;
                            $mark->setScore($score);
                            
                            $markRepository->add($mark);
                        }
                        
                    }
                    
                    if ($filext == 'c') {
                        $note = 0;
                        // Exercice valeur absolue
                        $process = new Process(['python', $this->getParameter('code_directory').'\\'.$fileNam, 5]);
                        $process->run();
                        // dump($this->getParameter('code_directory').'\\'.$fileNam);
                        $res = $process->getOutput();
                        $preRes = trim(preg_replace('/\s\s+/', ' ', $res));
                        // dump($res);
                        // dump($preRes);
                        $expectedValue1  = 10;
                        if ($preRes == $expectedValue1) {
                            $note+=50;
                        }
                        // second test
                        $process = new Process(['python', $this->getParameter('code_directory').'\\'.$fileNam, -5]);
                        $process->run();
                        // dump($this->getParameter('code_directory').'\\'.$fileNam);
                        $res = $process->getOutput();
                        $preRes = trim(preg_replace('/\s\s+/', ' ', $res));
                        // dump($res);
                        // dump($preRes);
                        $expectedValue1  = -10;
                        if ($preRes == $expectedValue1) {
                            $note+=50;
                        }

                        // check if note is already assign to the exo then update
                        $isMarkExist = $markRepository->findOneBy(['exo'=>$exo->getId(),'user'=> $this->getUser()]);
                        if ($isMarkExist != null) {
                            # code...
                            // get new score 
                            $newScore = $note;
                            $isMarkExist->setScore($newScore);
                            $markRepository->add($isMarkExist);
                        } else {
                            
                            $mark = new Mark();
                            $mark->setUser($this->getUser());
                            $mark->setExo($exo);
                            $mark->setStartedAt(new DateTimeImmutable('now'));
                            // get mark 
                            $score = $note;
                            $mark->setScore($score);
                            
                            $markRepository->add($mark);
                        }
                        
                    }
                    
                    $this->addFlash(
                       'info',
                       'Note disponible '
                    
                    );
                   
                    $lockcode->refresh();
                   
                


            } finally {
                $lockcode->release();
            }
        }
        
       



        $state = 'finish';
        
        return $this->render('mark/waiting_for_mark.html.twig', 
        [
            'user' =>  $this->getUser(),
            'state'=>$state,
         ]);
      
        

      
    }
    /**
     * @Route("/wait", name="app_wait_inde", methods={"GET"})
     */
    public function wait_f1( MarkRepository $markRepository, Request $request): Response
    {
        $ok = 3000;
        $id = (int)$request->query->get('id');
        
        // dump($form);
        // $form = $request->query->get('form');
        // $state = '';
        $state = 'process';
      
      
     
        // $dt = $this->increase(1000,3000);
        // if ($dt == 4000) {
        //     # code...
        //     return $this->redirectToRoute('app_wait_inde',[
        //         'user' =>  $this->getUser(),
        //         'state'=>$state,
        //     ]);
        // }
    
        return $this->render('mark/waiting_for_mark.html.twig', [
            'user' =>  $this->getUser(),
            'state'=>$state,
        ]);
       
          
    }
    /**
     * @Route("/wait", name="app_wait_ind", methods={"GET"})
     */
    public function wait_f2( MarkRepository $markRepository, Request $request): Response
    {
        $ok = 1;
        $id = (int)$request->query->get('id');
        
        // dump($form);
        // $form = $request->query->get('form');
       
        $state = 'finish';
   
       
      
        return $this->render('mark/waiting_for_mark.html.twig', [
     'user' =>  $this->getUser(),
     'state'=>$state,
      ]);

      
    }



    public function  increase ($nextt,$tt){

        while ($tt < $tt + $nextt) {
            $tt +=1;
        }
      

        return $tt;
    }

    /**
     * @Route("/new", name="app_mark_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MarkRepository $markRepository): Response
    {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $markRepository->add($mark);
            return $this->redirectToRoute('app_mark_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mark/new.html.twig', [
            'mark' => $mark,
            'form' => $form->createView(),'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_mark_show", methods={"GET"})
     */
    public function show(Mark $mark): Response
    {
        return $this->render('mark/show.html.twig', [
            'mark' => $mark,'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_mark_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Mark $mark, MarkRepository $markRepository): Response
    {
        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $markRepository->add($mark);
            return $this->redirectToRoute('app_mark_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mark/edit.html.twig', [
            'mark' => $mark,
            'form' => $form->createView(),'user' =>  $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_mark_delete", methods={"POST"})
     */
    public function delete(Request $request, Mark $mark, MarkRepository $markRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mark->getId(), $request->request->get('_token'))) {
            $markRepository->remove($mark);
        }

        return $this->redirectToRoute('app_mark_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Event;
use App\Entity\Feedback;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/event')]
class EventController extends AbstractController
{
    private User $connectedUser;

    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }

       ///////////////////////////////////////////////         /////////////////////////////////////////////////
      ///////////////////////////////////////////////         /////////////////////////////////////////////////
     /////////////////////////////////////////////// VISITOR /////////////////////////////////////////////////
    ///////////////////////////////////////////////         /////////////////////////////////////////////////
   ///////////////////////////////////////////////         /////////////////////////////////////////////////

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function indexVisitor(EntityManagerInterface $entityManager, EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $connectedUserID = -1;

        $showOtherEvents = 1;

        $searchTerm = $request->query->get('q');
        $name = $request->query->get('name');
        $feeOrder = $request->query->get('feeOrder');
        $status = $request->query->get('status');
        $type = $request->query->get('type');
    
         $events = $entityManager
             ->getRepository(Event::class)
             ->createQueryBuilder('e')
             ->andWhere('e.user != :val')
             ->setParameter('val', $connectedUserID)
             ->getQuery()
             ->getResult();
    
        if ($name) {
            $events = $eventRepository->findAllSortedByName($name, $connectedUserID, $showOtherEvents);
        } elseif ($feeOrder) {
            $events = $eventRepository->findAllSortedByPrice($feeOrder, $connectedUserID, $showOtherEvents);
        } elseif ($status) {
            $events = $eventRepository->findByStatus($status, $connectedUserID,$showOtherEvents);
        } elseif ($type) {
            $events = $eventRepository->findByType($type, $connectedUserID,$showOtherEvents);
        } elseif ($searchTerm) {
            $events = $eventRepository->findByTerm($searchTerm, $connectedUserID,$showOtherEvents);
        }

        $pages = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1),
            9
        );
    
        return $this->render('event/client/index.html.twig', [
            'events' => $pages,
            'searchTerm' => $searchTerm,
            'showOtherEvents' => $showOtherEvents,
        ]);
    }

       ///////////////////////////////////////////////       /////////////////////////////////////////////////
      ///////////////////////////////////////////////       /////////////////////////////////////////////////
     /////////////////////////////////////////////// ADMIN /////////////////////////////////////////////////
    ///////////////////////////////////////////////       /////////////////////////////////////////////////
   ///////////////////////////////////////////////       /////////////////////////////////////////////////

    #[Route('/admin', name: 'app_event_index_admin', methods: ['GET'])]
    public function indexAdmin(EntityManagerInterface $entityManager, EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $hasAccess = $this->AdminAccess();
        if ($hasAccess) {

            $connectedUserID = -1;

            $showOtherEvents = 1;
    
            $userID = $request->query->get('userID');

            $searchTerm = $request->query->get('q');
            $name = $request->query->get('name');
            $feeOrder = $request->query->get('feeOrder');
            $status = $request->query->get('status');
            $type = $request->query->get('type');

            $users = $entityManager
                ->getRepository(User::class)
                ->findAll();
            $events = $eventRepository->findAll();

            if ($userID) {
                $events = $eventRepository->findByUser($userID);
            }
            if ($name) {
                $events = $eventRepository->findAllSortedByName($name, $connectedUserID, $showOtherEvents);
            } elseif ($feeOrder) {
                $events = $eventRepository->findAllSortedByPrice($feeOrder, $connectedUserID, $showOtherEvents);
            } elseif ($status) {
                $events = $eventRepository->findByStatus($status, $connectedUserID,$showOtherEvents);
            } elseif ($type) {
                $events = $eventRepository->findByType($type, $connectedUserID,$showOtherEvents);
            } elseif ($searchTerm) {
                $events = $eventRepository->findByTerm($searchTerm, $connectedUserID,$showOtherEvents);
            }
    
            $pages = $paginator->paginate(
                $events, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                9 // Nombre de résultats par page
            );

            return $this->render('event/admin/index.html.twig', [
                'events' => $events,
                'users' => $users,
                // 'userID' => $userID,
                'searchTerm' => $searchTerm,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }  

    #[Route('/newAdmin', name: 'app_event_new_admin', methods: ['GET', 'POST'])]
    public function newAdmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->AdminAccess();
        if ($hasAccess) {

            $event = new Event();
            $event->setImage('http://localhost/PIDEV/BlogUploads/imagec.png');
            $event->setUser($this->connectedUser);

            $form = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                    $imageURL = $this->getParameter('file_base_url') . '/' . $newFilename;
                    $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
                
                    try {
                        $imageFile->move(
                            $this->getParameter('destinationPath'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // handle exception if something happens during file upload
                    }
                
                    $event->setImage($imageURL);
                }
                $entityManager->persist($event);
                $entityManager->flush();

                return $this->redirectToRoute('app_event_index_admin', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('event/admin/new.html.twig', [
                'event' => $event,
                'form' => $form,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }


    #[Route('/admin/{eventid}/edit', name: 'app_event_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->AdminAccess();
        if ($hasAccess) {

            $form = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                    $imageURL = $this->getParameter('file_base_url') . '/' . $newFilename;

                    $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
                
                    try {
                        $imageFile->move(
                            $this->getParameter('destinationPath'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // handle exception if something happens during file upload
                    }
                
                    $event->setImage($imageURL);
                }
            
                $entityManager->flush();

                return $this->redirectToRoute('app_event_index_admin', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('event/admin/edit.html.twig', [
                'event' => $event,
                'form' => $form,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/admin/{eventid}', name: 'app_event_show_admin', methods: ['GET'])]
    public function showAdmin(Event $event): Response
    {
        return $this->render('event/admin/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/admin/{eventid}', name: 'app_event_delete_admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->AdminAccess();
        if ($hasAccess) {

            if ($this->isCsrfTokenValid('delete'.$event->getEventid(), $request->request->get('_token'))) {
                $entityManager->remove($event);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_event_index_admin', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }
    
       ///////////////////////////////////////////////        /////////////////////////////////////////////////
      ///////////////////////////////////////////////        /////////////////////////////////////////////////
     /////////////////////////////////////////////// ARTIST /////////////////////////////////////////////////
    ///////////////////////////////////////////////        /////////////////////////////////////////////////
   ///////////////////////////////////////////////        /////////////////////////////////////////////////

    #[Route('/artist', name: 'app_event_index_artist', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $hasAccess = $this->ArtistAccess();
        if ($hasAccess) {

            $connectedUserID = $this->connectedUser->getUserId();
            $connectedUserRole = $this->connectedUser->getRole();

            $showOtherEvents = $request->query->get('showOtherEvents') == 1;

            // if ($connectedUserRole=== "artist") {

            $searchTerm = $request->query->get('q');
            $name = $request->query->get('name');
            $feeOrder = $request->query->get('feeOrder');
            $status = $request->query->get('status');
            $type = $request->query->get('type');
        
            if ($request->query->get('view') === 'otherEvents') {
                $showOtherEvents = true;
            }
        
            if (!$showOtherEvents) {
                $events = $eventRepository->findByUser($connectedUserID);
            } else {
                $events = $entityManager
                    ->getRepository(Event::class)
                    ->createQueryBuilder('e')
                    ->andWhere('e.user != :val')
                    ->setParameter('val', $this->connectedUser->getUserId())
                    ->getQuery()
                    ->getResult();
            }
        
            if ($name) {
                $events = $eventRepository->findAllSortedByName($name, $connectedUserID, $showOtherEvents);
            } elseif ($feeOrder) {
                $events = $eventRepository->findAllSortedByPrice($feeOrder, $connectedUserID, $showOtherEvents);
            } elseif ($status) {
                $events = $eventRepository->findByStatus($status, $connectedUserID,$showOtherEvents);
            } elseif ($type) {
                $events = $eventRepository->findByType($type, $connectedUserID,$showOtherEvents);
            } elseif ($searchTerm) {
                $events = $eventRepository->findByTerm($searchTerm, $connectedUserID,$showOtherEvents);
            }

            $pages = $paginator->paginate(
                $events,
                $request->query->getInt('page', 1),
                9
            );

              
            return $this->render('event/artist/index.html.twig', [
                'events' => $pages,
                'searchTerm' => $searchTerm,
                'showOtherEvents' => $showOtherEvents,
                // 'getParticipation' => $getParticipation,
            ]);
            // }
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }   
    
    #[Route('/artist/new', name: 'app_event_new_artist', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->ArtistAccess();
        if ($hasAccess) {

            $event = new Event();
            $event->setImage('http://localhost/PIDEV/BlogUploads/imagec.png');
            $event->setUser($this->connectedUser);

            $form = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = $form->getData();


                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                    $imageURL = $this->getParameter('file_base_url') . '/' . $newFilename;
                    $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
                
                    try {
                        $imageFile->move(
                            $this->getParameter('destinationPath'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // handle exception if something happens during file upload
                    }
                
                    $event->setImage($imageURL);
                }
                $entityManager->persist($event);
                $entityManager->flush();

                return $this->redirectToRoute('app_event_index_artist', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('event/artist/new.html.twig', [
                'event' => $event,
                'form' => $form,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/artist/{eventid}', name: 'app_event_show_artist', methods: ['GET'])]
    public function show(Event $event, EntityManagerInterface $entityManager, Request $request): Response
    {
        $hasAccess = $this->ArtistClientAccess();
        if ($hasAccess) {

            $connectedUserID = $this->connectedUser->getUserId();
            $connectedUserRole = $this->connectedUser->getRole();
            
            // check if the connected user is the owner of the event
            $isOwner = ($event->getUser()->getUserId() == $connectedUserID);
        
            // 
            $participation = $entityManager->getRepository(Participation::class)->findOneBy([
                'user' => $connectedUserID,
                'event' => $event->getEventid(),    
            ]);
        
            $feedback = $entityManager->getRepository(Feedback::class)->findOneBy([
                'user' => $connectedUserID,
                'event' => $event->getEventid(),    
            ]);
        
            $activities = $entityManager
                ->getRepository(Activity::class)
                ->createQueryBuilder('a')
                ->andWhere('a.event = :val')
                ->setParameter('val', $event)
                ->getQuery()
                ->getResult();

            return $this->render('event/artist/show.html.twig', [
                'event' => $event,
                'isOwner' => $isOwner,
                'participation' => $participation,
                'feedback' => $feedback,
                'activities' => $activities
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }
    

    #[Route('/artist/{eventid}/edit', name: 'app_event_edit_artist', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->ArtistAccess();
        if ($hasAccess) {

            $form = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                    $imageURL = $this->getParameter('file_base_url') . '/' . $newFilename;

                    $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
                
                    try {
                        $imageFile->move(
                            $this->getParameter('destinationPath'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // handle exception if something happens during file upload
                    }
                
                    $event->setImage($imageURL);
                }
            
                $entityManager->flush();

                return $this->redirectToRoute('app_event_index_artist', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('event/artist/edit.html.twig', [
                'event' => $event,
                'form' => $form,
            ]);
        
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/artist/{eventid}', name: 'app_event_delete_artist', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->ArtistAccess();
        if ($hasAccess) {

            if ($this->isCsrfTokenValid('delete'.$event->getEventid(), $request->request->get('_token'))) {
                $entityManager->remove($event);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_event_index_artist', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

       ///////////////////////////////////////////////        /////////////////////////////////////////////////
      ///////////////////////////////////////////////        /////////////////////////////////////////////////
     /////////////////////////////////////////////// CLIENT /////////////////////////////////////////////////
    ///////////////////////////////////////////////        /////////////////////////////////////////////////
   ///////////////////////////////////////////////        /////////////////////////////////////////////////

   #[Route('/client', name: 'app_event_index_client', methods: ['GET'])]
   public function indexx(EntityManagerInterface $entityManager, EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
   {
       $hasAccess = $this->ClientAccess();
       if ($hasAccess) {
           $connectedUserID = $this->connectedUser->getUserId();

           $showOtherEvents = 1;

           $searchTerm = $request->query->get('q');
           $name = $request->query->get('name');
           $feeOrder = $request->query->get('feeOrder');
           $status = $request->query->get('status');
           $type = $request->query->get('type');
       
            $events = $entityManager
                ->getRepository(Event::class)
                ->createQueryBuilder('e')
                ->andWhere('e.user != :val')
                ->setParameter('val', $this->connectedUser->getUserId())
                ->getQuery()
                ->getResult();
       
           if ($name) {
               $events = $eventRepository->findAllSortedByName($name, $connectedUserID, $showOtherEvents);
           } elseif ($feeOrder) {
               $events = $eventRepository->findAllSortedByPrice($feeOrder, $connectedUserID, $showOtherEvents);
           } elseif ($status) {
               $events = $eventRepository->findByStatus($status, $connectedUserID,$showOtherEvents);
           } elseif ($type) {
               $events = $eventRepository->findByType($type, $connectedUserID,$showOtherEvents);
           } elseif ($searchTerm) {
               $events = $eventRepository->findByTerm($searchTerm, $connectedUserID,$showOtherEvents);
           }

           $pages = $paginator->paginate(
               $events,
               $request->query->getInt('page', 1),
               9
           );
       
           return $this->render('event/client/index.html.twig', [
               'events' => $pages,
               'searchTerm' => $searchTerm,
               'showOtherEvents' => $showOtherEvents,
           ]);
           // }
        } else {
           return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
       }
   }   

    private function AdminAccess()
    {
        if ($this->connectedUser->getRole() == "admin") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
     private function ClientAccess()
    {
        if ($this->connectedUser->getRole() === "client") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    } 
    private function ArtistAccess()
    {
        if ($this->connectedUser->getRole() === "artist") {
           return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
    private function ArtistClientAccess()
    {
        if ($this->connectedUser->getRole() == "artist" || $this->connectedUser->getRole() == "client") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
}

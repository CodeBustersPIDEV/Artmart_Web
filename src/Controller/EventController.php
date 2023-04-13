<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/event')]
class EventController extends AbstractController
{
    
    #[Route("/sortedByName", name:"findAllSortedByName")]
    public function findAllSortedByName(EntityManagerInterface $entityManager, Request $request, EventRepository $eventRepository): Response
    {
        $name = $request->query->get('name');

        $events = $eventRepository->findAllSortedByName($name);

        $users = $entityManager
        ->getRepository(User::class)
        ->findAll();
        
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'users' => $users
        ]);
    }

    #[Route("/sortedByPrice", name:"findAllSortedByPrice")]
    public function findAllSortedByPrice(Request $request, EventRepository $eventRepository): Response
    {
        $feeOrder = $request->query->get('feeOrder');

        $events = $eventRepository->findAllSortedByPrice($feeOrder);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route("/sortedByType", name:"findByType")]
    public function findByType(Request $request, EventRepository $eventRepository): Response
    {
        $type = $request->query->get('type');

        $events = $eventRepository->findByType($type);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route("/sortedByStatus", name:"findByStatus")]
    public function findByStatus(Request $request, EventRepository $eventRepository): Response
    {
        $status = $request->query->get('status');

        $events = $eventRepository->findByStatus($status);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $searchTerm = $request->query->get('q');

        $events = $entityManager
            ->getRepository(Event::class)
            ->findAll();

        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();

        if ($searchTerm) {
            $events = $entityManager
            ->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->where('e.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
        }

        $pages = $paginator->paginate(
            $events, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            9 // Nombre de résultats par page
        );

        return $this->render('event/index.html.twig', [
            'events' => $pages,
            'users' => $users,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/otherEvents/{id}', name: 'app_event_otherEvents', methods: ['GET'])]
    public function findOtherEvents(EntityManagerInterface $entityManager, $id): Response
    {
        $events = $entityManager
            ->getRepository(Event::class)
            ->findOtherEvents($id);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/findMyEvents', name: 'app_event_my_events', methods: ['GET'])]
    public function findMyEvents(EntityManagerInterface $entityManager, EventRepository $eventRepository, Request $request): Response
    {
        $userID = $request->query->get('userID');
        
        $users = $entityManager
        ->getRepository(User::class)
        ->findAll();


        $events = $eventRepository->findByUser($userID);

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'users' => $users
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{eventid}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{eventid}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{eventid}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getEventid(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/activity')]
class ActivityController extends AbstractController
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
    #[Route('/admin', name: 'app_activity_index_admin', methods: ['GET'])]
    public function indexAdmin(EntityManagerInterface $entityManager): Response
    {
        $activities = $entityManager
            ->getRepository(Activity::class)
            ->findAll();

        return $this->render('activity/admin/index.html.twig', [
            'activities' => $activities,
        ]);
    }

    #[Route('/newAdmin', name: 'app_activity_new_admin', methods: ['GET', 'POST'])]
    public function newAdmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity/admin/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{activityid}', name: 'app_activity_show_admin', methods: ['GET'])]
    public function showAdmin(Activity $activity): Response
    {
        return $this->render('activity/admin/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    #[Route('/admin/{activityid}/edit', name: 'app_activity_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity/admin/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{activityid}', name: 'app_activity_delete_admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getActivityid(), $request->request->get('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity_index_admin', [], Response::HTTP_SEE_OTHER);
    }


//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////
    #[Route('/artist', name: 'app_activity_index_artist', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $activities = $entityManager
            ->getRepository(Activity::class)
            ->findAll();

        return $this->render('activity/artist/index.html.twig', [
            'activities' => $activities,
        ]);
    }

    #[Route('/artist/new', name: 'app_activity_new_artist', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity/artist/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{activityid}', name: 'app_activity_show_artist', methods: ['GET'])]
    public function show(Activity $activity): Response
    {
        return $this->render('activity/artist/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    #[Route('/artist/{activityid}/edit', name: 'app_activity_edit_artist', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity/artist/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{activityid}', name: 'app_activity_delete_artist', methods: ['POST'])]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getActivityid(), $request->request->get('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity_index_artist', [], Response::HTTP_SEE_OTHER);
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

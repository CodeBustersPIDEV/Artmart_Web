<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Artist;
use App\Entity\Client;
use App\Form\UserType;
use App\Repository\ArtistRepository;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Webpatser\Uuid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, ArtistRepository $artistRepository, ClientRepository $clientRepository): Response
    {
        $user = new User();
        $addedUser = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            $email = $form->get('email')->getData();

            if ($role === 'client') {
                // $user = new Client();
                $client = new Client();
                $client->setNbrDemands(0);
                $client->setNbrOrders(0);
            } elseif ($role === 'artist') {
                // $user = new Artist();
                $artist = new Artist();
                $artist->setNbrArtwork(0);
                $artist->setBio("");
            }

            // $user->setRole($role);
            $userRepository->save($user, true);

            if ($role === 'client') {
                $addedUser = $userRepository->findOneUserByEmail($email);
                $client->setUser($addedUser);
                $clientRepository->save($client, true);
            } elseif ($role === 'artist') {
                $addedUser = $userRepository->findOneUserByEmail($email);
                $artist->setUser($addedUser);
                $artistRepository->save($artist, true);
            }

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/{userId}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{userId}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{userId}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getUserId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

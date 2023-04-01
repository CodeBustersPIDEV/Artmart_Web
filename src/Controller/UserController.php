<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Artist;
use App\Entity\Client;
use App\Entity\Admin;
use App\Form\UserType;
use App\Repository\ArtistRepository;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, ArtistRepository $artistRepository, ClientRepository $clientRepository, AdminRepository $adminRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $addedUser = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            $user = $form->getData();
            $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('This email is already taken.'));
            } else {
                $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encodedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                $userId = $user->getUserId();
                $email = $form->get('email')->getData();
           }
            if ($role === 'client') {
                // $user = new Client();
                $client = new Client();
                $client->setNbrDemands(0);
                $client->setNbrOrders(0);
                $client->setUserId($userId);
                $client->setUser($user);
                $entityManager->persist($client);
                $entityManager->flush();
            } elseif ($role == 'artist') {
                $artist = new Artist();
                $artist->setNbrArtwork(0);
                $artist->setUserId($userId);
                $artist->setUser($user);
                $entityManager->persist($artist);
                $entityManager->flush();
            } elseif ($role == 'admin') {
                $admin = new Admin();
                $admin->setUserId($userId);
                $admin->setUser($user);
                $entityManager->persist($admin);
                $entityManager->flush();
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
            } elseif ($role === 'admin') {
                $addedUser = $userRepository->findOneUserByEmail($email);
                $admin->setUser($addedUser);
                $adminRepository->save($admin, true);
            }

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/{userId}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    {
        $client = $clientRepository->findOneBy(['user' => $user]);
        $artist = $artistRepository->findOneBy(['user' => $user]);
        $admin = $adminRepository->findOneBy(['user' => $user]);

        $role = $user->getRole();
        if ($role === 'client' && $client) {
            $clientAttributes = [
                'nbrOrders' => $client->getNbrOrders(),
                'nbrDemands' => $client->getNbrDemands(),
            ];
        } elseif ($role === 'artist' && $artist) {
            $artistAttributes = [
                'bio' => $artist->getBio(),
                'nbrArtwork' => $artist->getNbrArtwork(),
            ];
        }
        if ($role === 'admin' && $admin) {
            $adminAttributes = [
                'department' => $admin->getDepartment(),
            ];
        }
        if ($role === 'client') {
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'clientAttributes' => $clientAttributes ?? null,
            ]);
        } elseif ($role === 'artist') {
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'artistAttributes' => $artistAttributes ?? null,
            ]);
        } elseif ($role === 'admin') {
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'adminAttributes' => $adminAttributes ?? null,
            ]);
        }
    }

    #[Route('/{userId}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    {
        $client = $clientRepository->findOneBy(['user' => $user]);
        $artist = $artistRepository->findOneBy(['user' => $user]);
        $admin = $adminRepository->findOneBy(['user' => $user]);
        $role = $user->getRole();

        $clientAttributes = [
            'nbrOrders' => null,
            'nbrDemands' => null,
        ];
        $artistAttributes = [
            'bio' => null,
            'nbrArtwork' => null,
        ];
        $adminAttributes = [
            'department' => null,
        ];

        if ($role === 'client' && $client) {
            $clientAttributes = [
                'nbrOrders' => $client->getNbrOrders(),
                'nbrDemands' => $client->getNbrDemands(),
            ];
        } elseif ($role === 'artist' && $artist) {
            $artistAttributes = [
                'bio' => $artist->getBio(),
                'nbrArtwork' => $artist->getNbrArtwork(),
            ];
        } elseif ($role === 'admin' && $admin) {
            $adminAttributes = [
                'department' => $admin->getDepartment(),
            ];
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true,
            'client_attributes' => $clientAttributes,
            'artist_attributes' => $artistAttributes,
            'admin_attributes' => $adminAttributes,
        ]);

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
    public function delete(User $user, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository, EntityManagerInterface $entityManager): Response
    {
        $client = $clientRepository->findOneBy(['user' => $user]);
        $artist = $artistRepository->findOneBy(['user' => $user]);
        $admin = $adminRepository->findOneBy(['user' => $user]);

        $role = $user->getRole();
        if ($role === 'client' && $client) {
            $entityManager->remove($user);
            $entityManager->remove($client);
            $entityManager->flush();
        } elseif ($role === 'artist' && $artist) {
            $entityManager->remove($user);
            $entityManager->remove($artist);
            $entityManager->flush();
        } elseif ($role === 'admin' && $admin) {
            $entityManager->remove($user);
            $entityManager->remove($admin);
            $entityManager->flush();
        }


        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user')]
class UserController extends AbstractController
{

    public function uploadImage(UploadedFile $file, User $user): void
    {
        $destinationFilePath = $this->getParameter ('destinationPath');
        $newdestinationFilePath=$this->getParameter ('file_base_url');
        $filePath = sprintf('%s/%s', $newdestinationFilePath['host'], $newdestinationFilePath['path']);
        // Get the original filename of the uploaded file
        $filename = $file->getClientOriginalName();
        if (!is_uploaded_file($file->getPathname())) {
            throw new FileException('File was not uploaded via HTTP POST.');
        }

        if (!is_dir($destinationFilePath)) {
            // Create the directory
            mkdir($destinationFilePath, 0777, true);
        }
        // Move the uploaded file to the destination
        $file->move($destinationFilePath, $filename);
        $user->setPicture($filePath.'/'. $filename);
    }


    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('search');
        $userse = $request->query->get('userse');

        $queryBuilder = $entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('u');

        if ($userse === 'name') {
            $queryBuilder->orderBy('u.name', 'ASC');
        } elseif ($userse === 'username') {
            $queryBuilder->orderBy('u.username', 'ASC');
        }

        if ($searchTerm) {
            $queryBuilder->where('u.username LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $users = $queryBuilder->getQuery()->getResult();

        /*   
        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();
*/
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, ArtistRepository $artistRepository, ClientRepository $clientRepository, AdminRepository $adminRepository): Response
    {
        $user = new User();
        $addedUser = new User();
        $artist = new Artist();
        $admin = new Admin();

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            $user = $form->getData();
            $file = $form->get('file')->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $email = $form->get('email')->getData();
            $client = new Client();
            $this->uploadImage($file, $user);

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

            if ($role === 'client') {
                // $user = new Client();
                $client->setNbrDemands(0);
                $client->setNbrOrders(0);
                // $client->setUserId($userId);
                $client->setUser($user);
                $entityManager->persist($client);
                $entityManager->flush();
            } elseif ($role == 'artist') {

                $artist->setNbrArtwork(0);
                //  $artist->setUserId($userId);
                $artist->setUser($user);
                $entityManager->persist($artist);
                $entityManager->flush();
            } elseif ($role == 'admin') {

                //  $admin->setUserId($userId);
                $admin->setUser($user);
                $entityManager->persist($admin);
                $entityManager->flush();
            }

            return $this->redirectToRoute('/', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => false,
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
    #[Route('/{userId}/Profile', name: 'app_user_Profile', methods: ['GET'])]
    public function Profile(User $user, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
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
            return $this->render('user/Profile.html.twig', [
                'user' => $user,
                'clientAttributes' => $clientAttributes ?? null,
            ]);
        } elseif ($role === 'artist') {
            return $this->render('user/Profile.html.twig', [
                'user' => $user,
                'artistAttributes' => $artistAttributes ?? null,
            ]);
        } elseif ($role === 'admin') {
            return $this->render('user/Profile.html.twig', [
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

        if ($role === 'artist' && $artist) {
            $artistAttributes = [
                'bio' => $artist->getBio(),
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


        if ($role === 'artist' && $artist) {
            $bioField = $form->get('bio');
            $bioField->setData($artist->getBio());
        }

        // Set the initial value of the department field if the user is an admin
        if ($role === 'admin' && $admin) {
            $departmentField = $form->get('department');
            $departmentField->setData($admin->getDepartment());
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureField = $form->get('file')->getData();
            if ($pictureField == null) {
                $user->setPicture($user->getPicture());
            } else {
                $this->uploadImage($pictureField, $user);
            }
            if ($user->getRole() === 'artist' && $artist) {
                $bio = $form->get('bio')->getData();
                $artist->setBio($bio);
                $entityManager->persist($artist);
            } elseif ($user->getRole() === 'admin' && $admin) {
                $department = $form->get('department')->getData();
                $admin->setDepartment($department);
                $entityManager->persist($admin);
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => true,
            'client_attributes' => $clientAttributes,
            'artist_attributes' => $artistAttributes,
            'admin_attributes' => $adminAttributes,
        ]);
    }

    #[Route('/{userId}/editProfile', name: 'app_user_editProfile', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, User $user, EntityManagerInterface $entityManager, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
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

        if ($role === 'artist' && $artist) {
            $artistAttributes = [
                'bio' => $artist->getBio(),
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


        if ($role === 'artist' && $artist) {
            $bioField = $form->get('bio');
            $bioField->setData($artist->getBio());
        }

        // Set the initial value of the department field if the user is an admin
        if ($role === 'admin' && $admin) {
            $departmentField = $form->get('department');
            $departmentField->setData($admin->getDepartment());
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureField = $form->get('file')->getData();
            if ($pictureField == null) {
                $user->setPicture($user->getPicture());
            } else {
                $this->uploadImage($pictureField, $user);
            }
            if ($user->getRole() === 'artist' && $artist) {
                $bio = $form->get('bio')->getData();
                $artist->setBio($bio);
                $entityManager->persist($artist);
            } elseif ($user->getRole() === 'admin' && $admin) {
                $department = $form->get('department')->getData();
                $admin->setDepartment($department);
                $entityManager->persist($admin);
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_user_Profile', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editF.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => true,
            'client_attributes' => $clientAttributes,
            'artist_attributes' => $artistAttributes,
            'admin_attributes' => $adminAttributes,
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


        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }


}

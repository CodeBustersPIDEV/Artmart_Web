<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Artist;
use App\Entity\Admin;
use App\Entity\Client;
use App\Repository\ArtistRepository;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Repository\AdminRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class RegistrationController extends AbstractController
{
    public function uploadImage(UploadedFile $file, User $user): void
    {
        $destinationFilePath = $this->getParameter('destinationPath');
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
        $user->setPicture($destinationFilePath . $filename);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, UserRepository $userRepository, ArtistRepository $artistRepository, ClientRepository $clientRepository, AdminRepository $adminRepository): Response
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

        

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

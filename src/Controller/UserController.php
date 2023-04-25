<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Artist;
use App\Entity\Clients;
use App\Entity\Admin;
use App\Form\UserType;
use App\Repository\ArtistRepository;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Repository\AdminRepository;
use App\Form\TokenVerificationType;
use App\Form\VerificationEmailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\TwiML\Voice\Sms;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\Messages\Channel\SMS\SMSText;
use Vonage\Response\Message;
use Vonage\SMS\Message\SMS as MessageSMS;

#[Route('/user')]
class UserController extends AbstractController

{
    private Environment $twig;


    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function uploadImage(UploadedFile $file, User $user): void
    {
        $destinationFilePath = $this->getParameter('destinationPath');
        $newdestinationFilePath = $this->getParameter('file_base_url');
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
        $user->setPicture($filePath . '/' . $filename);
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
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, UserRepository $userRepository, ArtistRepository $artistRepository, ClientRepository $clientRepository, AdminRepository $adminRepository): Response
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
            $password = $form->get('password')->getData();
            $hashedPassword = hash('sha256', $password);
            $user = $form->getData();
            $token = bin2hex(random_bytes(16));
            $user->setToken($token);
            $user->setPassword($hashedPassword);
            $file = $form->get('file')->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $email = $form->get('email')->getData();
            $client = new Clients();
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
            $this->SendEmail($mailer, $user, $token, 'Verification Token');
            return $this->redirectToRoute('app_EnableAccount', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => false,
        ]);
    }

    public function SendEmail(Mailer $mailer, User $user, String $token, String $subject)
    {

        $email = (new TemplatedEmail())
            ->from(new Address('samar.hamdi@esprit.tn', 'Artmart'))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate('user/email_token.html.twig')
            ->context([
                'user' => $user,
                'token' => $token,
            ]);

        $sent = $mailer->send($email);

        if ($sent > 0) {
            $this->addFlash('success', 'Your registration token has been sent to your email.');
        } else {
            $this->addFlash('error', 'There was an error sending your registration token. Please try again later.');
        }
    }
    #[Route('/{userId}/show', name: 'app_user_show', methods: ['GET'])]
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
        $ProfilePic = $user->getPicture();

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
            'Pic' => $ProfilePic,
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
        $ProfilePic = $user->getPicture();
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
            'Pic' => $ProfilePic,
            'client_attributes' => $clientAttributes,
            'artist_attributes' => $artistAttributes,
            'admin_attributes' => $adminAttributes,
        ]);
    }


    #[Route('/{userId}/delete', name: 'app_user_delete', methods: ['POST'])]
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


        return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{userId}/deleteAd', name: 'app_user_deleteAd', methods: ['POST'])]
    public function deleteA(User $user, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository, EntityManagerInterface $entityManager): Response
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

    #[Route('/{userId}/block', name: 'app_user_block', methods: ['POST'])]
    public function block(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setBlocked(1);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{userId}/unblock', name: 'app_user_unblock', methods: ['POST'])]
    public function unblock(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setBlocked(0);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{userId}/ActivateAccount', name: 'app_user_EnableAccount', methods: ['GET'])]
    public function ActivateAccount(User $user, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $token = bin2hex(random_bytes(16));
        $user->setToken($token);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->SendEmail($mailer, $user, $token, 'Verification Token');
        return $this->redirectToRoute('app_user_TokenVerif', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{userId}/ForgetPwd', name: 'app_user_ForgetPwd', methods: ['GET'])]
    public function ForgetPwd(User $user, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $token = bin2hex(random_bytes(16));
        $user->setToken($token);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->SendEmail($mailer, $user, $token, 'Verification Token');
        return $this->redirectToRoute('app_user_TokenVerifPwd', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{userId}/TokenVerification', name: 'app_user_TokenVerif', methods: ['GET', 'POST'])]
    public function TokenVerification(User $user, EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {
        $tries = 3;

        $U = new User();
        $form = $this->createForm(TokenVerificationType::class, $U);
        $form->handleRequest($request);
        $userToken = $user->getToken();

        if ($form->isSubmitted()) {
            $token = $form->get('token')->getData();
            $warningMessage = $session->get('warning_message');
            if ($warningMessage) {
                $session->remove('warning_message');
            }
            if ($userToken == $token) {
                $user->setEnabled(true);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            } else {

                $session->set('warning_message', 'Incorrect token.No attempts left.');
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->renderForm('user/tokenVerif.html.twig', [
            'user' => $U,
            'userId' => $user->getUserId(),
            'form' => $form,

        ]);
    }


    #[Route('/{userId}/TokenVerificationPwd', name: 'app_user_TokenVerifPwd', methods: ['GET', 'POST'])]
    public function TokenVerificationPwd(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {

        $U = new User();
        $form = $this->createForm(TokenVerificationType::class, $U);
        $form->handleRequest($request);
        $userToken = $user->getToken();

        if ($form->isSubmitted()) {
            $token = $form->get('token')->getData();
            if ($userToken == $token) {
                $user->setEnabled(true);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_pwd_resetPwd', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }
        return $this->renderForm('user/tokenVerif.html.twig', [
            'user' => $U,
            'userId' => $user->getUserId(),
            'form' => $form,

        ]);
    }



    #[Route('/EmailVerif', name: 'app_user_EmailVerif', methods: ['GET', 'POST'])]
    public function EmailVerification(Request $request, UserRepository $userRepository,MailerInterface $mailer,EntityManagerInterface $entityManager): Response
    {
        $u = new User();
        $SearchedUser=new User();
        $form = $this->createForm(VerificationEmailType::class, $u);
        $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            $email = $form->get('email')->getData();

            $SearchedUser = $userRepository->findOneBy(['email'=>$email]);

            if ($SearchedUser && $SearchedUser->isEnabled()) {
                $token = bin2hex(random_bytes(16));
                $SearchedUser->setToken($token);
                $entityManager->persist($SearchedUser);
                $entityManager->flush();
                $this->SendEmail($mailer, $SearchedUser, $token, 'Verification Token');
                return $this->redirectToRoute('app_user_TokenVerifPwd', ['userId' => $SearchedUser->getUserId()], Response::HTTP_SEE_OTHER);
            } elseif ($SearchedUser && !$SearchedUser->isEnabled()) {
                $content = $this->twig->render('user/account_activation.html.twig', ['user_id' => $SearchedUser->getUserId()]);
                return new Response($content);
            } else {
                return $this->redirectToRoute('app_home');
            }

        
        }
        return $this->renderForm('user/emailVerif.html.twig', [
            'user' => $u,
            'form' => $form,

        ]);
    }
    #[Route('/{userId}/SMSVerif', name: 'app_user_SMSVerif', methods: ['GET', 'POST'])]
    public function SMSVerification(User $user,EntityManagerInterface $entityManager): Response

    {

        $basic  = new Basic('f871a0cc', 'Fx3aGR7W6cEy6qcQ');
        $client = new Client($basic);

        $code = mt_rand(1000, 9999);
        $user->setToken($code);
        $entityManager->persist($user);
        $entityManager->flush();
        $vpn = "216" . $user->getPhonenumber();
        $message = new SMSText($vpn, 'Vonage APIs', 'Hello this is your verification token'.$code);
        $result = $client->messages()->send($message);
        
   
            return $this->redirectToRoute('app_user_TokenVerifPwd', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
       
    
    }
}

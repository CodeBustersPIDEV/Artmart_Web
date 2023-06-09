<?php

namespace App\Controller;


use DateTime;
use Vonage\Client;
use App\Entity\User;
use App\Entity\Admin;
use Twig\Environment;
use App\Entity\Artist;
use App\Form\UserType;
use App\Entity\Clients;
use Vonage\SMS\Message\SMS;
use App\Repository\UserRepository;
use App\Form\TokenVerificationType;
use App\Form\VerificationEmailType;
use App\Repository\AdminRepository;
use App\Repository\ArtistRepository;
use App\Repository\ClientRepository;
use Symfony\Component\Mailer\Mailer;
use Vonage\Client\Credentials\Basic;
use Doctrine\ORM\EntityManagerInterface;
use Vonage\Messages\Channel\SMS\SMSText;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail as MimeTemplatedEmail;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/user')]
class UserController extends AbstractController

{
    private Environment $twig;
    private User $connectedUser;
    private $paginator;


    public function __construct(SessionInterface $session, UserRepository $userRepository, Environment $twig, PaginatorInterface $paginator)
    {
        $this->twig = $twig;
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
        $this->paginator = $paginator;
    }

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
        $user->setPicture('http://localhost/PIDEV/BlogUploads/'.$filename);
    }
    #[Route('/chart-data', name: 'chart_data')]
    public function userRolesChart(UserRepository $userRepository): Response
    {
        $adminCount = $userRepository->countUsersByRole('admin');
        $clientCount = $userRepository->countUsersByRole('client');
        $artistCount = $userRepository->countUsersByRole('artist');

        $data = [
            'labels' => ['Admin', 'Client', 'Artist'],
            'data' => [$adminCount, $clientCount, $artistCount],
        ];

        return new JsonResponse($data);
    }


    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hasAdminAccess = $this->AdminAccess();
        $searchTerm = $request->query->get('search');
        $userse = $request->query->get('userse');
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $pagination = $this->paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            8
        );

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

        if ($hasAdminAccess) {
            return $this->render('user/index.html.twig', [
                'users' => $pagination,
                'searchTerm' => $searchTerm,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
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
            return $this->redirectToRoute('app_user_EnableAccount', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => false,
        ]);
    }

    #[Route('/newAdmin', name: 'app_user_newAd', methods: ['GET', 'POST'])]
    public function newAdmin(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, AdminRepository $adminRepository): Response
    {
        $user = new User();
        $addedUser = new User();
        $admin = new Admin();
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false,
        ]);
        $form->handleRequest($request);
        echo ('test');
        if ($form->isSubmitted() && $form->isValid()) {

            $role = 'admin';
            $password = $form->get('password')->getData();
            $hashedPassword = hash('sha256', $password);
            $user = $form->getData();
            $token = bin2hex(random_bytes(16));
            $user->setToken($token);
            $user->setRole($role);
            $user->setEnabled(1);
            $user->setPassword($hashedPassword);
            $file = $form->get('file')->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $email = $form->get('email')->getData();
            $this->uploadImage($file, $user);

            // $user->setRole($role);
            $userRepository->save($user, true);

            $addedUser = $userRepository->findOneUserByEmail($email);
            $admin->setUser($addedUser);
            $adminRepository->save($admin, true);

            $admin->setUser($user);
            $entityManager->persist($admin);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('user/newAdmin.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => false,
        ]);
    }
    public function SendEmail(Mailer $mailer, User $user, String $token, String $subject)
    {

        $email = (new MimeTemplatedEmail())
        ->from('samar.hamdi@esprit.tn')
        ->to($user->getEmail())
        ->subject($subject)
        ->htmlTemplate('user/email_token.html.twig')
        ->context([
            'user' => $user,
            'token' => $token,
        ]);

    $mailer->send($email);

    }
    #[Route('/{userId}/show', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    {
        $hasAdminAccess = $this->AdminAccess();
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
        if ($hasAdminAccess) {
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
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }
    #[Route('/{userId}/Profile', name: 'app_user_Profile', methods: ['GET'])]
    public function Profile(User $user, RequestStack $requestStack, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    {
        $date = new DateTime();

        $hasAccess = $this->ArtistClientAccess();
        $currentUrl = $requestStack->getCurrentRequest()->getUri();
        $serverIpAddress = '127.0.0.1'; // replace with your server's IP address
        $pcIpAddress = getHostByName(getHostName());


        $newUrl = str_replace($serverIpAddress, $pcIpAddress, $currentUrl);
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
        $formatted_date = $date->format('Y-m-d H:i:s');
        if ($hasAccess) {
            if ($role === 'client') {
                return $this->render('user/Profile.html.twig', [
                    'user' => $user,
                    'clientAttributes' => $clientAttributes ?? null,
                    'lastloggedin' => $formatted_date,

                ]);
            } elseif ($role === 'artist') {
                return $this->render('user/Profile.html.twig', [
                    'user' => $user,
                    'artistAttributes' => $artistAttributes ?? null,
                    'url' => $newUrl,
                    'lastloggedin' => $formatted_date,


                ]);
            } elseif ($role === 'admin') {
                return $this->render('user/Profile.html.twig', [
                    'user' => $user,
                    'adminAttributes' => $adminAttributes ?? null,
                    'lastloggedin' => $formatted_date,

                ]);
            }
        } else {
            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{userId}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    {
        $hasAccess = $this->AdminAccess();
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
        if ($hasAccess) {
            return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form,
                'is_edit' => true,
                'Pic' => $ProfilePic,
                'client_attributes' => $clientAttributes,
                'artist_attributes' => $artistAttributes,
                'admin_attributes' => $adminAttributes,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{userId}/editProfile', name: 'app_user_editProfile', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, User $user, EntityManagerInterface $entityManager, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    {
        $hasAccess = $this->ArtistClientAccess();
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

        if ($form->isSubmitted() ) {
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
        if ($hasAccess) {
            return $this->renderForm('user/editF.html.twig', [
                'user' => $user,
                'form' => $form,
                'is_edit' => true,
                'Pic' => $ProfilePic,
                'client_attributes' => $clientAttributes,
                'artist_attributes' => $artistAttributes,
                'admin_attributes' => $adminAttributes,
            ]);
        } else {
            return $this->redirectToRoute('app_admin', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
        }
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
            'userId' => $user->getUserId(),
            'enabled' => $user->isEnabled(),
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
            'userId' => $user->getUserId(),

        ]);
    }



    #[Route('/EmailVerif', name: 'app_user_EmailVerif', methods: ['GET', 'POST'])]
    public function EmailVerification(Request $request, UserRepository $userRepository, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $u = new User();
        $SearchedUser = new User();
        $form = $this->createForm(VerificationEmailType::class, $u);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $email = $form->get('email')->getData();

            $SearchedUser = $userRepository->findOneBy(['email' => $email]);
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
    public function SMSVerification(User $user, EntityManagerInterface $entityManager): Response

    {

        $basic  = new Basic('f871a0cc', 'Fx3aGR7W6cEy6qcQ');
        $client = new Client($basic);

        $code = mt_rand(1000, 9999);
        $user->setToken($code);
        $entityManager->persist($user);
        $entityManager->flush();
        $vpn = "216" . $user->getPhonenumber();
        $message = [
            'to' => $vpn,
            'from' => 'ArtMart',
            'text' => 'Hello this is your verification token' . $code
        ];

        $client->message()->send($message);

        if ($user->isEnabled()) {
            return $this->redirectToRoute('app_user_TokenVerifPwd', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_user_TokenVerif', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);
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

    private function ArtistClientAccess()
    {
        if ($this->connectedUser->getRole() == "artist" || $this->connectedUser->getRole() == "client") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
}

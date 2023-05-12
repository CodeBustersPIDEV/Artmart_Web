<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Artist;
use App\Entity\Clients;
use App\Repository\UserRepository;
use App\Repository\AdminRepository;
use App\Repository\ArtistRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Vonage\Client\Credentials\Basic;
use Vonage\client;


#[Route('/api_user')]
class UserApiController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }
    #[Route('/user', name: 'app_userapi', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager
            ->getRepository(User::class)
            ->findAll();
        $responseArray = array();
        foreach ($user as $user) {
            $responseArray[] = array(

                'user_id'=>$user->getUserId(),
                'name' => $user->getName(),
                'username' => $user->getUsername(),
                'phonenumber' => $user->getPhonenumber(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'birthday' => $user->getBirthday(),
                'role' => $user->getRole(),
                'file'=>$user->getPicture().""
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    #[Route('/user/add', name: 'user', methods: ['GET', 'POST'])]
    public function adduser(Request $request, UserRepository $userRepository, ArtistRepository $artistRepository, ClientRepository $clientRepository, AdminRepository $adminRepository): JsonResponse
    { 
        
        
        $basic  = new Basic('f871a0cc', 'Fx3aGR7W6cEy6qcQ');
        $clients = new Client($basic);

        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $admin = new Admin();
        $client = new Clients();
        $artist = new Artist();
        $hashedPassword = hash('sha256', $request->request->get('password'));
        $code = mt_rand(1000, 9999);
        $user->setToken($code);
        $user->setName($request->request->get('name'));
        $user->setRole($request->request->get('role'));
        $user->setUsername($request->request->get('username'));
        $user->setPassword($hashedPassword);
        $user->setBirthday(new \DateTime($request->request->get('birthday')));
        $user->setEmail($request->request->get('email'));
        $user->setPhonenumber($request->request->get('phonenumber'));
        $user->setPicture("http://localhost/PIDEV/BlogUploads/user.png"."");
        $entityManager->persist($user);
        $entityManager->flush();   
       /* $vpn = "216" . $user->getPhonenumber();
        $message = [
            'to' => $vpn,
            'from' => 'ArtMart',
            'text' => 'Hello this is your verification token' . $code
        ];
        $clients->message()->send($message);
           */
        if ($user->getRole() === 'client') {
            $addedUser = $userRepository->findOneUserByEmail($user->getEmail());
            $client->setUser($addedUser);
            $clientRepository->save($client, true);
        } elseif ($user->getRole() === 'artist') {
            $addedUser = $userRepository->findOneUserByEmail($user->getEmail());
            $artist->setUser($addedUser);
            $artistRepository->save($artist, true);
        } elseif ($user->getRole() === 'admin') {
            $addedUser = $userRepository->findOneUserByEmail($user->getEmail());
            $admin->setUser($addedUser);
            $adminRepository->save($admin, true);
        }
       

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }
   

    
    #[Route('/user/{id}', name: 'user_edit', methods: ['PUT'])]
    public function edituser(Request $request, User $user, ArtistRepository $artistRepository, AdminRepository $adminRepository): JsonResponse
    {

        $entityManager = $this->getDoctrine()->getManager();

        $artist = $artistRepository->findOneBy(['user' => $user]);
        $admin = $adminRepository->findOneBy(['user' => $user]);
        $role = $user->getRole();
        if (!$user) {
            return new JsonResponse(['status' => 'Failed']);
        }
       
        if ($role === 'artist' && $artist) {
        $artist->getBio($request->request->get('bio'));
        }

        // Set the initial value of the department field if the user is an admin
        if ($role === 'admin' && $admin) {
            $admin->getDepartment($request->request->get('department'));
        }
        $user->setName($request->request->get('name'));
        $user->setUsername($request->request->get('username'));
        $user->setPhonenumber($request->request->get('phonenumber'));
        $user->setBirthday(new \DateTime($request->request->get('birthday')));
    
        if ($role === 'artist' && $artist) {
            $artist->setBio($request->request->get('bio'));
            $entityManager->persist($artist);

        }
    
        if ($role === 'admin' && $admin) {
            $admin->setDepartment($request->request->get('department'));
            $entityManager->persist($admin);

        }
    
        $user->setName($request->request->get('name'));
        $user->setUsername($request->request->get('username'));
        $user->setPhonenumber($request->request->get('phonenumber'));
        $user->setPicture($request->request->get('file'));
        $user->setEmail($request->request->get('email'));

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/user/signin', name: 'user_login', methods: ['POST'])]
   
public function signinAction(Request $request)
{
    $username = $request->query->get("username");
    $hashedPassword = hash('sha256', $request->request->get('password'));
   
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);

    if ($user) {
        if ($hashedPassword == $user->getPassword()) {
            return new JsonResponse(['success' => true, 'data' => $user->getUserId()]);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        return new JsonResponse(['success' => false, 'message' => 'User not found']);
    }
}   


    


    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST','DELETE'])]
    public function delete( User $user, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }
        $entityManager = $this->getDoctrine()->getManager();
        if ($user->getRole() == 'client') {
            $client = $clientRepository->findOneBy(['user' => $user]);
            $entityManager->remove($client);
            $entityManager->remove($user);
            $entityManager->flush();
        }
        if ($user->getRole() == 'artist') {
            $artist = $artistRepository->findOneBy(['user' => $user]);
            $entityManager->remove($artist);
            $entityManager->remove($user);
            $entityManager->flush();
        }
        if ($user->getRole() == 'admin') {
            $admin = $adminRepository->findOneBy(['user' => $user]);
            $entityManager->remove($admin);
            $entityManager->remove($user);
            $entityManager->flush();
        }




        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }
    #[Route('/user/verifyToken/{id}', name: 'user_verif', methods: ['POST'])]
    public function verifyToken( User $user,Request $request): Response
    {      
        
        $token = $request->query->get("token");

        if ($user) {
            if ($token == $user->getToken()) {
                return new JsonResponse(['success' => true, 'data' => $user->getUserId()]);
            } else {
                return new JsonResponse(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            return new JsonResponse(['success' => false, 'message' => 'User not found']);
        }



        $response = new JsonResponse(['status' => 'verified'], Response::HTTP_OK);
        return $response;
    }


    #[Route('/user/block/{id}', name: 'user_block', methods: ['POST'])]
    public function block( User $user,Request $request): Response
    {      
        
    
        $response = new JsonResponse(['status' => 'blocked'], Response::HTTP_OK);
        return $response;
    }
    #[Route('/user/unblocked/{id}', name: 'user_unblock', methods: ['POST','DELETE'])]
    public function unblock( User $user,Request $request): Response
    {      
        
    
        $response = new JsonResponse(['status' => 'blocked'], Response::HTTP_OK);
        return $response;
    }
}

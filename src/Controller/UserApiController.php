<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Artist;
use App\Entity\Clients;
use App\Entity\Categories;
use App\Repository\UserRepository;
use App\Repository\AdminRepository;
use App\Repository\ArtistRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api_user')]
class UserApiController extends AbstractController
{

    #[Route('/user', name: 'app_userapi', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager
            ->getRepository(User::class)
            ->findAll();
        $responseArray = array();
        foreach ($user as $user) {
            $responseArray[] = array(
                'User_id' => $user->getUserId(),
                'name' => $user->getName(),
                'username' => $user->getUsername(),
                'phonenumber' => $user->getPhonenumber(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'enabled' => $user->isEnabled(),
                'blocked' => $user->isBlocked(),
                'birthday' => $user->getBirthday(),
                'role' => $user->getRole(),
                'file'=>$user->getPicture()
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
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $admin = new Admin();
        $client = new Clients();
        $artist = new Artist();
        $hashedPassword = hash('sha256', $request->request->get('password'));
        $token = bin2hex(random_bytes(16));
        $user->setToken($token);
        $user->setName($request->request->get('name'));
        $user->setRole($request->request->get('role'));
        $user->setUsername($request->request->get('username'));
        $user->setPassword($hashedPassword);
        $birthday = \DateTime::createFromFormat('Y-m-d', $request->request->get('birthday'));
        $user->setBirthday($birthday);
        $user->setPicture($request->request->get('file'));
        $user->setEmail($request->request->get('email'));
        $user->setPhonenumber($request->request->get('phonenumber'));

        $entityManager->persist($user);
        $entityManager->flush();
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
    #[Route('/user/addAd', name: 'user_AD', methods: ['GET', 'POST'])]
    public function adduserAd(Request $request, UserRepository $userRepository, ArtistRepository $artistRepository, ClientRepository $clientRepository, AdminRepository $adminRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $admin = new Admin();
        $client = new Clients();
        $artist = new Artist();
        $hashedPassword = hash('sha256', $request->request->get('password'));
        $token = bin2hex(random_bytes(16));
        $user->setToken($token);
        $user->setName($request->request->get('name'));
        $user->setRole('admin');
        $user->setUsername($request->request->get('username'));
        $user->setPassword($hashedPassword);
        $birthday = \DateTime::createFromFormat('Y-m-d', $request->request->get('birthday'));
        $user->setBirthday($birthday);
        $user->setPicture($request->request->get('file'));
        $user->setEmail($request->request->get('email'));
        $user->setPhonenumber($request->request->get('phonenumber'));

        $entityManager->persist($user);
        $entityManager->flush();
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
        $birthday = \DateTime::createFromFormat('Y-m-d', $request->request->get('birthday'));
        $user->setBirthday($birthday);
        $user->setPicture($request->request->get('file'));
        $user->setEmail($request->request->get('email'));

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    public function signinAction(Request $request){

        $username = $request->query->get("username");
        $password = $request->query->get("password");

        $em= $this->getDoctrine()->getManager();
        $user= $em->getRepository(User::class)->findOneBy(['username'=>$username]);// find user by email

        if($user){
            if(password_verify($password,$user->getPassword())) {
                 $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
            else{
                return new Response("password not found");
            }

            }
        else{
            return new Response("failed");

        }
    }


    public function editpassword(Request $request, UserRepository $userRepository): Response
    {      $hashedPassword = hash('sha256', $request->query->get("password"));
        
        $email = $request->query->get("email");
        
               
        $em=$this->getDoctrine()->getManager();
        $user =$this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email'=>$email]);
       
           $user->setPassword($hashedPassword);
           $user->setEmail($email);
        
           
                   
       try {
           $em = $this->getDoctrine()->getManager ();
           $em->persist($user);
           $em->flush();
           return new JsonResponse("Password was updates successfully",200);//290 ya3ni http result tas server OK
       }catch (\Exception $ex) {
           return new Response("failed ".$ex->getMessage());
       }
    }


    public function delete(Request $request, User $user, ClientRepository $clientRepository, ArtistRepository $artistRepository, AdminRepository $adminRepository): Response
    { if (!$user) {
        throw $this->createNotFoundException('The user does not exist');
    } $entityManager = $this->getDoctrine()->getManager();
        if($user->getRole()=='admin'){
        $client = $clientRepository->findOneBy(['user' => $user]);
        $entityManager->remove($client);
        $entityManager->flush();
        } if($user->getRole()=='admin'){
        $artist = $artistRepository->findOneBy(['user' => $user]);
        $entityManager->remove($artist);
        $entityManager->flush();
    } if($user->getRole()=='admin'){
        $admin = $adminRepository->findOneBy(['user' => $user]);
        $entityManager->remove($admin);
        $entityManager->flush();
    }
       
       
        

    $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    return $response;
    }

}

<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Form\LoginType;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request)
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('username')->getData();
            $password = $form->get('password')->getData();
    
        $isValid = $this->validateCredentials($username, $password);

        if ($isValid) {
            $session = $request->getSession();
            $session->set('username', $username);

            return $this->redirectToRoute('Panel');
        }
    }

    return $this->render('security/login.html.twig', [
        'loginForm' => $form->createView(),
    ]);
}

#[Route('/session', name: 'app_session')]
public function test(Request $request,EntityManagerInterface $entityManager)
{
$session = $request->getSession();

$userId = $session->get('user_id');

if (!$userId) {
    return $this->redirectToRoute('login');
}

$user = $entityManager->getRepository(User::class)->find($userId);

return new Response($user);
}

#[Route('/logout', name: 'app_logout')]
public function logout(Request $request)
{
    $session->set('user_id', $user->getUserId());
    return new Response("Logout Success");
}
}
<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\LoginType;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, SessionInterface $session, AuthenticationUtils $authenticationUtils)
    {
        
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('email')->getData();
            $password = $form->get('password')->getData();
    
        // Validate the user's credentials against your user database
        $isValid = $this->validateCredentials($username, $password);

        if ($isValid) {
            // Start a new session and store the user's information
            $session = $request->getSession();
            $session->set('username', $username);

            // Redirect the user to their dashboard
            return $this->redirectToRoute('Panel');
        }

        // If the user's credentials are invalid, display an error message
        $error = 'Invalid username or password';
    } else {
        $error = $authenticationUtils->getLastAuthenticationError();
    }

    return $this->render('security/login.html.twig', [
        'loginForm' => $form->createView(),
        'error' => $error,
    ]);
}




private function validateCredentials($username, $password)
{
    $userRepository = $this->getDoctrine()->getRepository(User::class);

    // Get the user from the database based on the submitted username
    $user = $userRepository->findOneBy(['username' => $username]);

    // If the user doesn't exist or the password is incorrect, return false
    if (!$user || !password_verify($password, $user->getPassword())) {
        return false;
    }

    // If the user exists and the password is correct, return true
    return true;
}
}
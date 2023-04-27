<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Twig\Environment;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    
    private $entityManager;
    private Environment $twig;

    public function __construct(private UrlGeneratorInterface $urlGenerator,private LoggerInterface $logger,EntityManagerInterface $entityManager,Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;

    }

    public function authenticate(Request $request): Passport
    {
        $userIdentifier = $request->request->get('login')['username'];
        $password = $request->request->get('login')['password'];
        if ($userIdentifier === null) {
            $userIdentifier = 'default_user_identifier';
        }
        
        $userLoader = function ($userIdentifier) {
            $user = new User();
            return $user;
        };
    
        $userBadge = new UserBadge($userIdentifier, $userLoader);
    
        // Create a new Passport object with the UserBadge
        $passport = new Passport(
            $userBadge,
            new PasswordCredentials($request->request->get('login')['password']),
            [new CsrfTokenBadge('authenticate', $request->get('login')['_token'])]
        );
        return $passport;
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $userIdentifier = $request->request->get('login')['username'];
        $password = $request->request->get('login')['password'];
    
        $data = [
            'message' => sprintf('Authentication failed for user '.$userIdentifier.' with password '.$password.'.'),
        ];
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $userIdentifier]);
        
        if($user != null){
            $hashedPassword = hash('sha256', $password);
          if($user->isEnabled() && !$user->isBlocked() ){
            if($hashedPassword==$user->getPassword()){
            $session = $request->getSession();
            $session->set('user_id', $user->getUserId());
            $session->set('user_role', $user->getRole());
            $user->setLastLogin(new \DateTime());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            if ($user->getRole()=='client'||$user->getRole()=='artist'){
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }elseif($user->getRole()=='admin')
        {
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));

        }else 
            return new JsonResponse("Password incorrect");
        }}
        elseif(!$user->isEnabled()){
            
            $content = $this->twig->render('user/account_activation.html.twig', ['user_id' => $user->getUserId()]);
                return new Response($content);

        } elseif($user->isBlocked()){
            
            return new JsonResponse("your account is blocked");
        }
        }
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
    
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

}

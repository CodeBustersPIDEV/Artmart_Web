<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditPwdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/pwd')]
class EditPwdController extends AbstractController
{
#[Route('/{userId}/editPwd', name: 'app_pwd_editPwd', methods: ['GET', 'POST'])]
    public function editPwd(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditPwdType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $Pwd=$form->get('password')->getData();
            $hashedPassword = hash('sha256', $Pwd);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_Profile', ['userId' => $user->getUserId()], Response::HTTP_SEE_OTHER);

        }
        return $this->renderForm('editPwd/editPwd.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/{userId}/resetPwd', name: 'app_pwd_resetPwd', methods: ['GET', 'POST'])]
    public function resetPwd(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditPwdType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $Pwd=$form->get('password')->getData();
            $hashedPassword = hash('sha256', $Pwd);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);

        }
        return $this->renderForm('editPwd/editPwd.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

}
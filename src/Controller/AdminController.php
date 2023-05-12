<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\User;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $admins = $entityManager
            ->getRepository(Admin::class)
            ->findAll();

        return $this->render('admin/index.html.twig', [
            'admins' => $admins,
        ]);
    }

    #[Route('/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $admin = new Admin();
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form = $this->createForm(AdminType::class, $admin);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user->setName($form->get('name')->getData());
        $user->setEmail($form->get('email')->getData());
        $user->setBirthday($form->get('birthday')->getData());
        $user->setPhonenumber($form->get('phonenumber')->getData());
        $user->setUsername($form->get('username')->getData());
        $user->setPassword($form->get('password')->getData());
        $user->setRole($form->get('role')->getData());

        $admin->setDepartment($form->get('departement')->getData());
        $admin->setUser($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->persist($admin);
        $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/{adminId}', name: 'app_admin_show', methods: ['GET'])]
    public function show(Admin $admin): Response
    {
        return $this->render('admin/show.html.twig', [
            'admin' => $admin,
        ]);
    }

    #[Route('/{adminId}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Admin $admin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/{adminId}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Admin $admin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $admin->getAdminId(), $request->request->get('_token'))) {
            $entityManager->remove($admin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}

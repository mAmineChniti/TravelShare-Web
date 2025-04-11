<?php

namespace App\Controller;

use App\Entity\Guides;
use App\Form\GuidesType;
use App\Repository\GuidesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class GuidesController extends AbstractController
{
    #[Route('/guides', name: 'app_guides_index')]
    public function index(): Response
    {
        return $this->render('guides/index.html.twig');
    }

    #[Route('/guides/read', name: 'app_guides_read')]
    public function read(GuidesRepository $guidesRepository): Response
    {
        $guides = $guidesRepository->findAll();
        return $this->render('guides/read.html.twig', [
            'guides' => $guides,
        ]);
    }

    #[Route('/guides/add', name: 'app_guides_add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $guide = new Guides();
        $form = $this->createForm(GuidesType::class, $guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($guide);
            $em->flush();

            $this->addFlash('success', 'Guide added successfully!');
            return $this->redirectToRoute('app_guides_read');
        }

        return $this->render('guides/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/guides/{guideId}/show', name: 'app_guides_show')]
    public function show(Guides $guide): Response
    {
        return $this->render('guides/show.html.twig', [
            'guide' => $guide,
        ]);
    }

    #[Route('/guides/edit/{guideId}', name: 'app_guides_edit')]
    public function edit(Request $request, GuidesRepository $repo, EntityManagerInterface $em, int $guideId): Response
    {
        $guide = $repo->find($guideId);
        if (!$guide) {
            throw $this->createNotFoundException('Guide not found');
        }
    
        $form = $this->createForm(GuidesType::class, $guide);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_guides_read');
        }
    
        return $this->render('guides/edit.html.twig', [
            'form' => $form,
            'guide' => $guide,
        ]);
    }
    

    #[Route('/guides/{guideId}/delete', name: 'app_guides_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        int $guideId,
        GuidesRepository $guidesRepository,
        ManagerRegistry $doctrine
    ): Response {
        $guide = $guidesRepository->find($guideId);
        
        if (!$guide) {
            throw $this->createNotFoundException('Guide not found');
        }
    
        if ($this->isCsrfTokenValid('delete'.$guideId, $request->request->get('_token'))) {
            $em = $doctrine->getManager();
            $em->remove($guide);
            $em->flush();
            $this->addFlash('success', 'Guide deleted successfully!');
        }
    
        return $this->redirectToRoute('app_guides_read');
    }

    #[Route('/guides/about', name: 'app_guides_about')]
    public function about(): Response
    {
        return $this->render('guides/about.html.twig');  // Better to use render() for templates
    }
}
<?php

namespace App\Controller;

use App\Entity\Promo;
use App\Repository\PromoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PromoController extends AbstractController
{
    #[Route('/dashboard/promo', name: 'app_promo')]
    public function index(PromoRepository $promoRepository): Response
    {
        $promos = $promoRepository->listAll();

        return $this->render('dashboard/promo/index.html.twig', [
            'promos' => $promos,
        ]);
    }

    #[Route('/dashboard/promo/add', name: 'app_promo_add')]
    public function add(PromoRepository $promoRepository, Request $request, ValidatorInterface $validator): Response
    {
        if ($request->isMethod('POST')) {
            $promo = new Promo();
            $promo->setCodepromo($request->request->get('codepromo'));
            $promo->setDateexpiration(new \DateTime($request->request->get('dateexpiration')));
            $promo->setPourcentagepromo($request->request->get('pourcentagepromo'));
            $promo->setNombremaxpersonne($request->request->get('nombremaxpersonne'));

            $errors = $validator->validate($promo);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                $this->addFlash('error', implode(', ', $errorMessages));
                return $this->render('dashboard/promo/add.html.twig', [
                    'errors' => $errorMessages,
                ]);
            }

            $promoRepository->add($promo);
            $this->addFlash('success', 'Promo added successfully!');

            return $this->redirectToRoute('app_promo');
        }

        return $this->render('dashboard/promo/add.html.twig');
    }

    #[Route('/dashboard/promo/edit/{id}', name: 'app_promo_edit')]
    public function edit(int $id, PromoRepository $promoRepository, Request $request, ValidatorInterface $validator): Response
    {
        $promo = $promoRepository->find($id);

        if (!$promo) {
            throw $this->createNotFoundException('Promo not found');
        }

        if ($request->isMethod('POST')) {
            $promo->setCodepromo($request->request->get('codepromo'));
            $promo->setDateexpiration(new \DateTime($request->request->get('dateexpiration')));
            $promo->setPourcentagepromo($request->request->get('pourcentagepromo'));
            $promo->setNombremaxpersonne($request->request->get('nombremaxpersonne'));

            $errors = $validator->validate($promo);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                $this->addFlash('error', implode(', ', $errorMessages));
                return $this->redirectToRoute('app_promo_edit', [
                    'promo' => $promo,
                    'errors' => $errorMessages
                ]);
            }
            $promoRepository->update($promo);

            $this->addFlash('success', 'Promo updated successfully!');

            return $this->redirectToRoute('app_promo');
        }

        return $this->render('dashboard/promo/edit.html.twig', [
            'promo' => $promo,
        ]);
    }

    #[Route('/dashboard/promo/delete/{id}', name: 'app_promo_delete')]
    public function delete(int $id, PromoRepository $promoRepository): Response
    {
        $promo = $promoRepository->find($id);

        if (!$promo) {
            $this->addFlash('error', 'Promo not found');
            return $this->redirectToRoute('app_promo');
        }
        $promoRepository->delete($promo);
        $this->addFlash('success', 'Promo deleted successfully!');
        return $this->redirectToRoute('app_promo');
    }
}

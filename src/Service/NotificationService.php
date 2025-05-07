<?php
// src/Service/NotificationService.php
namespace App\Service;

use App\Repository\ExcursionsRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class NotificationService
{
    public function __construct(
        private ExcursionsRepository $excursionsRepository,
        private RequestStack $requestStack
    ) {}

    public function checkPastExcursions(): array
{
    $pastExcursions = [];
    $now = new \DateTime();
    
    $allExcursions = $this->excursionsRepository->findAll();
    
    foreach ($allExcursions as $excursion) {
        if ($excursion->getDateExcursion() < $now) {
            $pastExcursions[] = $excursion;
            $this->requestStack->getSession()->getFlashBag()->add(
                'warning',
                sprintf('Excursion "%s" est terminée depuis le %s',
                    $excursion->getTitle(),
                    $excursion->getDateExcursion()->format('d/m/Y')
                )
            );
        }
    }

    return $pastExcursions;
}
public function getFormattedNotifications(): array
{
    $pastExcursions = $this->checkPastExcursions();
    
    return array_map(function($excursion) {
        return [
            'type' => 'warning',
            'icon' => 'exclamation-triangle',
            'message' => sprintf('Excursion "%s" est terminée depuis le %s', 
                $excursion->getTitle(),
                $excursion->getDateExcursion()->format('d/m/Y')
            ),
            'createdAt' => $excursion->getDateExcursion()
        ];
    }, $pastExcursions);
}
}
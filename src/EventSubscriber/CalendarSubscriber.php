<?php

namespace App\EventSubscriber;

use CalendarBundle\Entity\Event;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use App\Repository\OffresVoyageRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $offresVoyageRepository;
    private $router;

    public function __construct(OffresVoyageRepository $offresVoyageRepository, RouterInterface $router)
    {
        $this->offresVoyageRepository = $offresVoyageRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        $flights = $this->offresVoyageRepository->findAllOffres();
        if (empty($flights)) {
            return;
        }

        foreach ($flights as $flight) {
            $flightEvent = new Event(
                $flight->getTitre(),
                $flight->getDateDepart(),
            );

            $flightEvent->setOptions([
                'backgroundColor' => 'blue',
                'borderColor' => 'blue',
            ]);

            $flightEvent->addOption(
                'url',
                $this->router->generate('app_flight_details', [
                    'id' => $flight->getOffresVoyageId(),
                ])
            );

            $calendar->addEvent($flightEvent);
        }
    }
}

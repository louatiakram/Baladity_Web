<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\EvenementRepository; // Import the EventRepository
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
        ]);
    }

    #[Route('/calendar', name: 'calendar')]
    public function calendar(EvenementRepository $eventRepository): Response
    {
        // Assuming your Event entity has properties like id, start, etc.
        $events = $eventRepository->findAll();
    
        $rdvs = [];
    
        foreach ($events as $event) {
            $startDate = $event->getDateDHE();
            $endDate = $event->getDateDHF();
    
            // Format start and end dates with time component
            $startDateTime = $startDate->format('Y-m-d H:i:s');
            $endDateTime = $endDate->format('Y-m-d H:i:s');
    
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $startDateTime,
                'end' => $endDateTime,
                'title' => $event->getNomE(),
                'name' => $event->getNomE(), 
                'category' => $event->getCategorieE(), // Add category information
                // Adjust other properties based on your Event entity
            ];
        }
    
        $data = json_encode($rdvs);
    
        return $this->render('calendar/calendar.html.twig', compact('data'));
    }


}
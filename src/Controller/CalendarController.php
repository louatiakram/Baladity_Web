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
    
            // Iterate through each day between start and end dates
            $interval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day')); // Adding 1 day to include the end date
    
            foreach ($dateRange as $date) {
                $rdvs[] = [
                    'id' => $event->getId(),
                    'start' => $date->format('Y-m-d') . 'T' . $startDate->format('H:i:s'),
                    'end' => $date->format('Y-m-d') . 'T' . $endDate->format('H:i:s'),
                    'name' => $event->getNomE(),
                    // Adjust other properties based on your Event entity
                ];
            }
        }
    
        $data = json_encode($rdvs);
    
        return $this->render('calendar/calendar.html.twig', compact('data'));
    }
    





}
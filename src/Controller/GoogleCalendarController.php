<?php

namespace App\Controller;

use Google_Client;
use Google_Service_Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleCalendarController extends AbstractController
{
    #[Route('/google/calendar/events', name: 'google_calendar_events')]
    public function listEvents(): Response
    {
        // Check if credentials file exists and is readable
        $credentialsFile = '../config/credentials.json';
        if (!is_readable($credentialsFile)) {
            return new Response('Error: Credentials file not found or not readable.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Create Google Client
        $client = new Google_Client();
        $client->setAuthConfig($credentialsFile);
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
        $calendarService = new \Google_Service_Calendar($client);
        $calendars = $calendarService->calendarList->listCalendarList();

        // Authenticate with a service account
        // $client->setAccessType('offline'); // Uncomment if you want to access Google Calendar without user interaction

        try {
            // Get Google Calendar service
            $service = new Google_Service_Calendar($client);

            // Get events from Google Calendar
            $calendarId = 'primary'; // Use 'primary' for the primary calendar of the authenticated user
            $optParams = [
                'maxResults' => 10, // Adjust as needed
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => date('c'), // Show events starting from now
            ];
            $events = $service->events->listEvents($calendarId, $optParams)->getItems();

            // Pass events to the template
            return $this->render('google_calendar/events.html.twig', [
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            // Handle authentication or API errors
            return new Response('Error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendarApi;
use Google\Service\Calendar\Event as GoogleCalendarEvent;
use Google\Service\Calendar\EventDateTime;

class GoogleCalendarService
{
    private ?GoogleCalendarApi $calendarService = null;
    private ?string $calendarId;

    public function __construct()
    {
        $this->calendarId = env('GOOGLE_CALENDAR_ID');

        $credentialsFile = storage_path('app/'.env('GOOGLE_CALENDAR_CREDENTIALS_FILE', 'google-calendar-credentials.json'));

        if (empty($this->calendarId) || ! file_exists($credentialsFile)) {
            // Not configured yet — createEvent() will just return null below,
            // so tasks/meetings still save fine, they just won't show as synced.
            return;
        }

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFile);
        $client->addScope(GoogleCalendarApi::CALENDAR);

        $this->calendarService = new GoogleCalendarApi($client);
    }

    /**
     * Creates an event on the shared group calendar and returns its Google
     * event ID, or null if the calendar isn't configured or the call fails.
     */
    public function createEvent(string $title, ?string $description, Carbon $start, ?Carbon $end = null): ?string
    {
        if (! $this->calendarService || empty($this->calendarId)) {
            return null;
        }

        $end ??= $start->copy()->addHour();

        $event = new GoogleCalendarEvent([
            'summary' => $title,
            'description' => $description,
            'start' => new EventDateTime(['dateTime' => $start->toRfc3339String()]),
            'end' => new EventDateTime(['dateTime' => $end->toRfc3339String()]),
        ]);

        try {
            $createdEvent = $this->calendarService->events->insert($this->calendarId, $event);

            return $createdEvent->getId();
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}

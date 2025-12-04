<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_events_with_pagination_and_filters(): void
    {
        Event::factory()->create([
            'title' => 'Laravel Summit',
            'city' => 'Malang',
            'category' => 'conference',
            'status' => 'scheduled',
        ]);

        Event::factory()->create([
            'title' => 'UI/UX Meetup',
            'city' => 'Surabaya',
            'category' => 'meetup',
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/events?limit=1&search=Laravel&orderBy=start_date&sortBy=desc');

        $response->assertOk()
            ->assertJsonPath('per_page', 1)
            ->assertJsonPath('data.0.title', 'Laravel Summit');
    }

    public function test_can_show_single_event(): void
    {
        $event = Event::factory()->create();

        $this->getJson("/api/events/{$event->id}")
            ->assertOk()
            ->assertJsonPath('id', $event->id)
            ->assertJsonPath('title', $event->title);
    }

    public function test_can_create_event(): void
    {
        $payload = [
            'title' => 'Mini Bootcamp Product Design',
            'category' => 'workshop',
            'description' => 'Latihan membuat prototype aplikasi.',
            'location' => 'Ruang Inovasi Lt.3',
            'city' => 'Malang',
            'start_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'capacity' => 30,
            'ticket_price' => 15000,
            'status' => 'scheduled',
            'is_featured' => true,
        ];

        $this->postJson('/api/events', $payload)
            ->assertCreated()
            ->assertJsonPath('data.title', $payload['title'])
            ->assertJsonPath('data.available_seats', 30);

        $this->assertDatabaseHas('events', [
            'title' => $payload['title'],
            'capacity' => 30,
            'available_seats' => 30,
        ]);
    }

    public function test_can_update_event_with_capacity_changes(): void
    {
        $event = Event::factory()->create([
            'title' => 'Career Ready Workshop',
            'capacity' => 100,
            'available_seats' => 60,
            'status' => 'scheduled',
        ]);

        $payload = [
            'title' => 'Career Ready Workshop 2025',
            'capacity' => 80,
            'status' => 'scheduled',
        ];

        $this->patchJson("/api/events/{$event->id}", $payload)
            ->assertOk()
            ->assertJsonPath('data.title', $payload['title'])
            ->assertJsonPath('data.capacity', 80)
            ->assertJsonPath('data.available_seats', 60);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => $payload['title'],
            'capacity' => 80,
            'available_seats' => 60,
        ]);
    }

    public function test_can_soft_delete_event(): void
    {
        $event = Event::factory()->create();

        $this->deleteJson("/api/events/{$event->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Event deleted successfully.');

        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }
}

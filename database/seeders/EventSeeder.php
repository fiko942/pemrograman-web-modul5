<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Laravel Community Summit',
                'category' => 'conference',
                'description' => 'Sesi berbagi praktik terbaik pengembangan aplikasi Laravel untuk komunitas kampus.',
                'location' => 'Auditorium Dome',
                'city' => 'Malang',
                'start_date' => now()->addDays(7),
                'capacity' => 150,
                'available_seats' => 150,
                'ticket_price' => 0,
                'status' => 'scheduled',
                'is_featured' => true,
            ],
            [
                'title' => 'Career Ready Workshop',
                'category' => 'workshop',
                'description' => 'Workshop penulisan CV dan portofolio untuk mahasiswa Informatika.',
                'location' => 'Lab Programming 1',
                'city' => 'Malang',
                'start_date' => now()->addDays(14),
                'capacity' => 40,
                'available_seats' => 32,
                'ticket_price' => 25000,
                'status' => 'scheduled',
                'is_featured' => false,
            ],
            [
                'title' => 'Tech Meetup E-Sport Community',
                'category' => 'meetup',
                'description' => 'Diskusi santai lintas jurusan soal kolaborasi project game kompetitif.',
                'location' => 'Student Center',
                'city' => 'Surabaya',
                'start_date' => now()->addDays(3),
                'capacity' => 80,
                'available_seats' => 58,
                'ticket_price' => 5000,
                'status' => 'scheduled',
                'is_featured' => false,
            ],
        ];

        foreach ($events as $payload) {
            Event::create(array_merge($payload, [
                'slug' => Str::slug($payload['title']) . '-' . Str::random(5),
            ]));
        }
    }
}

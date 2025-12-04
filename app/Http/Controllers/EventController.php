<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    private const SORTABLE_COLUMNS = ['start_date', 'ticket_price', 'created_at', 'title', 'city'];
    private const CATEGORIES = ['conference', 'workshop', 'webinar', 'meetup', 'community'];
    private const STATUSES = ['scheduled', 'cancelled', 'completed'];

    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 10), 1), 50);
        $sortBy = strtolower($request->query('sortBy', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderBy = in_array($request->query('orderBy'), self::SORTABLE_COLUMNS, true)
            ? $request->query('orderBy')
            : 'start_date';

        $events = Event::query();

        if ($search = $request->query('search')) {
            $events->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $events->where('category', $category);
        }

        if ($status = $request->query('status')) {
            $events->where('status', $status);
        }

        $events->orderBy($orderBy, $sortBy);

        $paginated = $events->paginate($limit)->appends($request->query());

        return response()->json($paginated);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request);
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['status'] = $data['status'] ?? 'scheduled';
        $data['is_featured'] = $data['is_featured'] ?? false;
        $data['available_seats'] = $data['available_seats'] ?? $data['capacity'];

        $event = Event::create($data);

        return response()->json([
            'message' => 'Event created successfully.',
            'data' => $event,
        ], 201);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json($event);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $this->validatePayload($request, $event, isUpdate: true);

        if (isset($data['title'])) {
            $event->title = $data['title'];
            $event->slug = $this->generateUniqueSlug($event->title, $event->id);
        }

        foreach (Arr::except($data, ['title']) as $key => $value) {
            $event->{$key} = $value;
        }

        if (array_key_exists('available_seats', $data) === false && array_key_exists('capacity', $data)) {
            $event->available_seats = min($event->capacity, $event->available_seats);
        }

        $event->save();

        return response()->json([
            'message' => 'Event updated successfully.',
            'data' => $event,
        ]);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully.',
        ]);
    }

    private function validatePayload(Request $request, ?Event $event = null, bool $isUpdate = false): array
    {
        $existingCapacity = $event?->capacity;
        $required = $isUpdate ? 'sometimes' : 'required';

        $rules = [
            'title' => [$required, 'string', 'max:150'],
            'category' => [$required, Rule::in(self::CATEGORIES)],
            'description' => [$required, 'string'],
            'location' => [$required, 'string', 'max:120'],
            'city' => [$required, 'string', 'max:120'],
            'start_date' => [$required, 'date'],
            'capacity' => [$required, 'integer', 'min:1'],
            'available_seats' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                function (string $attribute, $value, $fail) use ($request, $existingCapacity) {
                    $capacity = $request->input('capacity', $existingCapacity);
                    if ($capacity !== null && $value > (int) $capacity) {
                        $fail('The available seats may not exceed the capacity.');
                    }
                },
            ],
            'ticket_price' => [$required, 'integer', 'min:0'],
            'status' => ['sometimes', Rule::in(self::STATUSES)],
            'is_featured' => ['sometimes', 'boolean'],
        ];

        return $request->validate($rules);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (
            Event::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

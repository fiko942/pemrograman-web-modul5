<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Todo::query();

        $query->when($request->query('search'), function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });

        $query->when($request->query('status'), fn($q, $status) => $q->where('status', $status));
        $query->when($request->query('category'), fn($q, $category) => $q->where('category', $category));
        $query->when($request->query('priority'), fn($q, $priority) => $q->where('priority', $priority));

        $todos = $query->paginate((int) $request->query('limit', 10));

        return response()->json($todos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,done',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|integer|min:1|max:5',
            'category' => 'nullable|in:personal,work,study,others',
        ]);

        $todo = Todo::create($data);

        return response()->json([
            'message' => 'Todo created successfully',
            'data' => $todo,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        return response()->json($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:pending,in_progress,done',
            'due_date' => 'sometimes|nullable|date',
            'priority' => 'sometimes|nullable|integer|min:1|max:5',
            'category' => 'sometimes|nullable|in:personal,work,study,others',
        ]);

        $todo->update($data);

        return response()->json([
            'message' => 'Todo updated successfully',
            'data' => $todo->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted successfully',
        ]);
    }
}

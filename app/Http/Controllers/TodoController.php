<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    /**
     * Tampilkan daftar todo dengan filter pencarian dasar dan pagination.
     *
     * @param Request $request permintaan HTTP yang membawa parameter filter.
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
     * Simpan todo baru lengkap dengan validasi dan lampiran opsional.
     *
     * @param Request $request permintaan HTTP yang membawa data todo.
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
            'attachment' => 'sometimes|file|mimes:jpg,jpeg,png,pdf,txt|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $this->storeAttachment($request->file('attachment'));
        }

        unset($data['attachment']);

        $todo = Todo::create($data);

        return response()->json([
            'message' => 'Todo created successfully',
            'data' => $todo,
        ], 201);
    }

    /**
     * Detail satu todo.
     *
     * @param Todo $todo entitas todo hasil binding model.
     */
    public function show(Todo $todo)
    {
        return response()->json($todo);
    }

    /**
     * Perbarui todo beserta opsi mengganti atau menghapus lampiran.
     *
     * @param Request $request permintaan HTTP yang membawa perubahan todo.
     * @param Todo $todo entitas todo hasil binding model.
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
            'attachment' => 'sometimes|file|mimes:jpg,jpeg,png,pdf,txt|max:2048',
            'remove_attachment' => 'sometimes|boolean',
        ]);

        $removeAttachment = $request->boolean('remove_attachment');
        unset($data['attachment'], $data['remove_attachment']);

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($todo->attachment_path);
            $data['attachment_path'] = $this->storeAttachment($request->file('attachment'));
        } elseif ($removeAttachment) {
            $this->deleteAttachment($todo->attachment_path);
            $data['attachment_path'] = null;
        }

        $todo->update($data);

        return response()->json([
            'message' => 'Todo updated successfully',
            'data' => $todo->fresh(),
        ]);
    }

    /**
     * Hapus todo beserta file lampiran jika ada.
     *
     * @param Todo $todo entitas todo hasil binding model.
     */
    public function destroy(Todo $todo)
    {
        $this->deleteAttachment($todo->attachment_path);
        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted successfully',
        ]);
    }

    /**
     * Unduh lampiran yang terasosiasi dengan todo tertentu.
     *
     * @param Todo $todo entitas todo hasil binding model.
     */
    public function downloadAttachment(Todo $todo)
    {
        if (!$todo->attachment_path || !Storage::disk('public')->exists($todo->attachment_path)) {
            return response()->json([
                'message' => 'Attachment not found',
            ], 404);
        }

        return Storage::disk('public')->download($todo->attachment_path);
    }

    /**
     * Simpan file lampiran ke disk publik dan kembalikan path relatif.
     *
     * @param UploadedFile $file berkas yang diunggah dari request.
     */
    private function storeAttachment(UploadedFile $file): string
    {
        return $file->store('attachments', 'public');
    }

    /**
     * Hapus file lampiran jika ada di storage.
     *
     * @param string|null $path path relatif lampiran yang akan dihapus.
     */
    private function deleteAttachment(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

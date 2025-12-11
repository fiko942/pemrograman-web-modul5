<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Todo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'priority',
        'category',
        'attachment_path',
        'file_path',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Properti tambahan yang akan ditampilkan di respons JSON.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'attachment_url',
    ];

    /**
     * Bangun URL publik untuk lampiran jika tersedia.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }
}

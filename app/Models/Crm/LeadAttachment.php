<?php

namespace App\Models\Crm;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LeadAttachment extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_attachments';

    protected $fillable = [
        'lead_id',
        'file_name',
        'file_original_name',
        'file_path',
        'file_type',
        'file_extension',
        'file_size',
        'uploaded_by',
    ];

    protected $appends = [
        'is_image',
        'is_previewable',
        'public_url',
        'formatted_file_size',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->file_type, 'image/');
    }

    public function getIsPreviewableAttribute(): bool
    {
        return $this->is_image || $this->file_extension === 'pdf';
    }

    public function getPublicUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = max((int) $this->file_size, 0);
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? min((int) floor(log($bytes, 1024)), count($units) - 1) : 0;

        return number_format($bytes / (1024 ** $power), $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }
}

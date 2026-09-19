<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LeadAttachment extends Model
{
    protected $fillable = ['original_name', 'path', 'mime_type', 'size', 'type'];

    protected static function booted(): void
    {
        static::deleting(function (LeadAttachment $attachment): void {
            Storage::disk('public')->delete($attachment->path);
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FollowupAttachment extends Model
{
    protected $fillable = ['original_name', 'path', 'mime_type', 'size', 'type'];

    protected static function booted(): void
    {
        static::deleting(function (FollowupAttachment $attachment): void {
            Storage::disk('public')->delete($attachment->path);
        });
    }

    public function followup()
    {
        return $this->belongsTo(Followup::class);
    }
}

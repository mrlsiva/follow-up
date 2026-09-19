<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    use HasFactory;
    protected $fillable = ['lead_id', 'user_id', 'followup_at', 'note', 'type', 'next_followup_at', 'status_after'];
    protected $casts = ['followup_at' => 'datetime', 'next_followup_at' => 'datetime'];
    public function lead() { return $this->belongsTo(Lead::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function attachment() { return $this->hasOne(FollowupAttachment::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'full_name', 'mobile_number', 'email', 'company_name', 'address', 'city', 'state', 'pincode', 'lead_source_id', 'service_id', 'status', 'priority', 'next_followup_at', 'remarks'];
    protected $casts = ['next_followup_at' => 'datetime'];
    public function followups() { return $this->hasMany(Followup::class); }
    public function attachments() { return $this->hasMany(LeadAttachment::class); }
    public function source() { return $this->belongsTo(LeadSource::class, 'lead_source_id'); }
    public function service() { return $this->belongsTo(Service::class); }
    public function owner() { return $this->belongsTo(User::class, 'user_id'); }
}

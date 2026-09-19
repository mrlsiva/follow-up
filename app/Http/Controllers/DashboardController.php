<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\LeadController as ApiLeadController;
use App\Models\{Lead, LeadSource, Service};
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadsImport;

class DashboardController extends Controller
{
    public function index() { return view('dashboard', ['summary' => ApiLeadController::summary(), 'leads' => Lead::with('source')->latest()->take(6)->get()]); }
    public function leads(Request $request) { $query = Lead::with(['source', 'service'])->latest(); if ($request->filled('search')) $query->where(fn($q) => $q->where('full_name', 'like', '%'.$request->search.'%')->orWhere('mobile_number', 'like', '%'.$request->search.'%')->orWhere('company_name', 'like', '%'.$request->search.'%')); if ($request->filled('status')) $query->where('status', $request->status); return view('leads.index', ['leads' => $query->paginate(12)->withQueryString(), 'sources' => LeadSource::orderBy('name')->get(), 'statuses' => ['New','Contacted','Interested','Follow-up','Converted','Lost']]); }
    public function create() { return view('leads.form', ['lead' => new Lead, 'sources' => LeadSource::orderBy('name')->get(), 'services' => Service::orderBy('name')->get()]); }
    public function importForm() { return view('leads.import'); }
    public function import(Request $request) { $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240']); Excel::import(new LeadsImport($request->user()), $request->file('file')); return redirect()->route('leads.index')->with('success', 'Import complete. Duplicate mobile numbers were skipped.'); }
    public function store(Request $request) { $data = $this->data($request); $data['user_id'] = $request->user()->id; $lead = Lead::create($data); $this->storeAttachments($request, $lead); return redirect()->route('leads.index')->with('success', 'Lead added to your pipeline.'); }
    public function edit(Lead $lead) { return view('leads.form', ['lead' => $lead, 'sources' => LeadSource::orderBy('name')->get(), 'services' => Service::orderBy('name')->get()]); }
    public function update(Request $request, Lead $lead) { $lead->update($this->data($request, $lead)); $this->storeAttachments($request, $lead); return redirect()->route('leads.show', $lead)->with('success', 'Lead updated.'); }
    public function show(Lead $lead) { return view('leads.show', ['lead' => $lead->load(['source', 'service', 'attachments', 'followups.attachment', 'followups' => fn($q) => $q->latest('followup_at')])]); }
    public function destroy(Lead $lead) { $lead->delete(); return redirect()->route('leads.index')->with('success', 'Lead removed.'); }
    public function destroyAttachment(Lead $lead, \App\Models\LeadAttachment $attachment) { abort_unless($attachment->lead_id === $lead->id, 404); $attachment->delete(); return back()->with('success', 'Attachment removed.'); }
    public function followupAttachment(Lead $lead, \App\Models\Followup $followup) { abort_unless($followup->lead_id === $lead->id && $followup->attachment, 404); return response()->file(Storage::disk('public')->path($followup->attachment->path), ['Content-Disposition' => 'inline; filename="'.addslashes($followup->attachment->original_name).'"']); }
    public function followup(Request $request, Lead $lead) { $data = $request->validate(['followup_at' => 'required|date', 'note' => 'required|string', 'type' => 'required|in:Call,WhatsApp,Meeting,Email', 'next_followup_at' => 'nullable|date', 'status_after' => 'nullable|string', 'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx']); $file = $request->file('attachment'); unset($data['attachment']); $followup = $lead->followups()->create($data + ['user_id' => $request->user()->id]); if ($file instanceof UploadedFile) { $path = $file->store('followup-attachments/'.$followup->id, 'public'); $followup->attachment()->create(['original_name' => $file->getClientOriginalName(), 'path' => $path, 'mime_type' => $file->getClientMimeType(), 'size' => $file->getSize(), 'type' => str_starts_with((string) $file->getMimeType(), 'image/') ? 'photo' : 'document']); } $lead->update(['status' => $data['status_after'] ?? $lead->status, 'next_followup_at' => $data['next_followup_at'] ?? null]); return back()->with('success', 'Follow-up logged.'); }
    private function data(Request $request, ?Lead $lead = null): array { return $request->validate(['full_name' => 'required|string|max:255', 'mobile_number' => 'required|string|max:30|unique:leads,mobile_number,'.($lead?->id ?? 'NULL'), 'email' => 'nullable|email', 'company_name' => 'nullable|string|max:255', 'address' => 'nullable|string', 'city' => 'nullable|string|max:100', 'state' => 'nullable|string|max:100', 'pincode' => 'nullable|string|max:12', 'lead_source_id' => 'nullable|exists:lead_sources,id', 'service_id' => 'nullable|exists:services,id', 'status' => 'required|in:New,Contacted,Interested,Follow-up,Converted,Lost', 'priority' => 'required|in:High,Medium,Low', 'next_followup_at' => 'nullable|date', 'remarks' => 'nullable|string', 'attachments' => 'nullable|array', 'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx']); }
    private function storeAttachments(Request $request, Lead $lead): void { foreach ($request->file('attachments', []) as $file) { if (!$file instanceof UploadedFile) continue; $path = $file->store('lead-attachments/'.$lead->id, 'public'); $lead->attachments()->create(['original_name' => $file->getClientOriginalName(), 'path' => $path, 'mime_type' => $file->getClientMimeType(), 'size' => $file->getSize(), 'type' => str_starts_with((string) $file->getMimeType(), 'image/') ? 'photo' : 'document']); } }
}

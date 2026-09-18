<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Followup, Lead, LeadSource, Service};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadsImport;

class LeadController extends Controller
{
    public function dashboard() { return response()->json($this->summary()); }
    public function index(Request $request) {
        $query = Lead::with(['source', 'service'])->latest();
        if ($term = $request->string('search')->toString()) $query->where(fn($q) => $q->where('full_name', 'like', "%$term%")->orWhere('mobile_number', 'like', "%$term%")->orWhere('company_name', 'like', "%$term%"));
        foreach (['status', 'lead_source_id'] as $filter) if ($request->filled($filter)) $query->where($filter, $request->$filter);
        return response()->json($query->paginate($request->integer('per_page', 15)));
    }
    public function show(Lead $lead) { return response()->json($lead->load(['source', 'service', 'followups' => fn($q) => $q->latest('followup_at'), 'followups.user'])); }
    public function store(Request $request) { $lead = Lead::create($this->validated($request)); return response()->json($lead->load(['source', 'service']), 201); }
    public function update(Request $request, Lead $lead) { $lead->update($this->validated($request, $lead)); return response()->json($lead->fresh(['source', 'service'])); }
    public function destroy(Lead $lead) { $lead->delete(); return response()->json(['message' => 'Lead deleted']); }
    public function followup(Request $request, Lead $lead) {
        $data = $request->validate(['followup_at' => 'required|date', 'note' => 'required|string', 'type' => 'required|in:Call,WhatsApp,Meeting,Email', 'next_followup_at' => 'nullable|date', 'status_after' => 'nullable|string']);
        $data['user_id'] = $request->user()->id; $followup = $lead->followups()->create($data);
        $lead->update(['status' => $data['status_after'] ?? $lead->status, 'next_followup_at' => $data['next_followup_at'] ?? null]);
        return response()->json($followup, 201);
    }
    public function today() { return response()->json(Lead::with('source')->whereDate('next_followup_at', today())->orderBy('next_followup_at')->get()); }
    public function import(Request $request) { $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240']); Excel::import(new LeadsImport($request->user()), $request->file('file')); return response()->json(['message' => 'Import complete']); }
    private function validated(Request $request, ?Lead $lead = null): array { return $request->validate(['full_name' => 'required|string|max:255', 'mobile_number' => 'required|string|max:30|unique:leads,mobile_number,' . ($lead?->id ?? 'NULL'), 'email' => 'nullable|email', 'company_name' => 'nullable|string|max:255', 'address' => 'nullable|string', 'city' => 'nullable|string|max:100', 'state' => 'nullable|string|max:100', 'pincode' => 'nullable|string|max:12', 'lead_source_id' => 'nullable|exists:lead_sources,id', 'service_id' => 'nullable|exists:services,id', 'status' => 'nullable|in:New,Contacted,Interested,Follow-up,Converted,Lost', 'priority' => 'nullable|in:High,Medium,Low', 'next_followup_at' => 'nullable|date', 'remarks' => 'nullable|string']); }
    public static function summary(): array { return ['total_leads' => Lead::count(), 'today_followups' => Lead::whereDate('next_followup_at', today())->count(), 'overdue_followups' => Lead::whereNotNull('next_followup_at')->where('next_followup_at', '<', now())->whereNotIn('status', ['Converted', 'Lost'])->count(), 'converted' => Lead::where('status', 'Converted')->count(), 'lost' => Lead::where('status', 'Lost')->count(), 'sources' => Lead::select('lead_source_id', DB::raw('count(*) as total'))->with('source:id,name,color')->groupBy('lead_source_id')->get()]; }
}

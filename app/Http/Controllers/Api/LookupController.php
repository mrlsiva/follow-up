<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{LeadSource, Service};
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function sources() { return response()->json(LeadSource::withCount('leads')->orderBy('name')->get()); }
    public function storeSource(Request $request) { $data = $request->validate(['name' => 'required|string|max:100|unique:lead_sources,name', 'color' => 'nullable|string|max:20']); return response()->json(LeadSource::create($data), 201); }
    public function showSource(LeadSource $leadSource) { return response()->json($leadSource->loadCount('leads')); }
    public function updateSource(Request $request, LeadSource $leadSource) { $data = $request->validate(['name' => 'required|string|max:100|unique:lead_sources,name,'.$leadSource->id, 'color' => 'nullable|string|max:20']); $leadSource->update($data); return response()->json($leadSource->fresh()->loadCount('leads')); }
    public function destroySource(LeadSource $leadSource) { $leadSource->delete(); return response()->json(['message' => 'Lead source deleted']); }

    public function services() { return response()->json(Service::withCount('leads')->orderBy('name')->get()); }
    public function storeService(Request $request) { $data = $request->validate(['name' => 'required|string|max:100|unique:services,name']); return response()->json(Service::create($data), 201); }
    public function showService(Service $service) { return response()->json($service->loadCount('leads')); }
    public function updateService(Request $request, Service $service) { $data = $request->validate(['name' => 'required|string|max:100|unique:services,name,'.$service->id]); $service->update($data); return response()->json($service->fresh()->loadCount('leads')); }
    public function destroyService(Service $service) { $service->delete(); return response()->json(['message' => 'Service deleted']); }
}

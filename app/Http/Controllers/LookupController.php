<?php

namespace App\Http\Controllers;

use App\Models\{LeadSource, Service};
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function sources() { return view('lookups.index', ['type' => 'source', 'title' => 'Lead sources', 'items' => LeadSource::withCount('leads')->orderBy('name')->get()]); }
    public function services() { return view('lookups.index', ['type' => 'service', 'title' => 'Interested services', 'items' => Service::withCount('leads')->orderBy('name')->get()]); }
    public function create(string $type) { abort_unless(in_array($type, ['source', 'service']), 404); return view('lookups.form', ['type' => $type, 'item' => $type === 'source' ? new LeadSource : new Service]); }
    public function store(Request $request, string $type) { abort_unless(in_array($type, ['source', 'service']), 404); $item = $type === 'source' ? LeadSource::create($this->data($request, $type)) : Service::create($this->data($request, $type)); return redirect()->route($type === 'source' ? 'settings.sources' : 'settings.services')->with('success', ucfirst($type).' created.'); }
    public function edit(string $type, int $id) { abort_unless(in_array($type, ['source', 'service']), 404); $item = $type === 'source' ? LeadSource::findOrFail($id) : Service::findOrFail($id); return view('lookups.form', ['type' => $type, 'item' => $item]); }
    public function update(Request $request, string $type, int $id) { abort_unless(in_array($type, ['source', 'service']), 404); $item = $type === 'source' ? LeadSource::findOrFail($id) : Service::findOrFail($id); $item->update($this->data($request, $type, $item->id)); return redirect()->route($type === 'source' ? 'settings.sources' : 'settings.services')->with('success', ucfirst($type).' updated.'); }
    public function destroy(string $type, int $id) { abort_unless(in_array($type, ['source', 'service']), 404); $item = $type === 'source' ? LeadSource::findOrFail($id) : Service::findOrFail($id); $item->delete(); return back()->with('success', ucfirst($type).' deleted.'); }
    private function data(Request $request, string $type, ?int $id = null): array { $table = $type === 'source' ? 'lead_sources' : 'services'; $data = $request->validate(['name' => 'required|string|max:100|unique:'.$table.',name,'.($id ?? 'NULL')]); if ($type === 'source') $data['color'] = $request->input('color', '#4DA3A7'); return $data; }
}

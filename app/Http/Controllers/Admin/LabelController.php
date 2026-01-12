<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labels = Label::withCount('tickets')->paginate(10);
        return view('admin.labels.index', compact('labels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.labels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:labels'],
        ]);

        Label::create([
            'name' => $request->name,
        ]);

        return redirect()->route('labels.index')->with('success', 'Label created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Label $label)
    {
        $label->load('tickets');
        return view('admin.labels.show', compact('label'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Label $label)
    {
        return view('admin.labels.edit', compact('label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Label $label)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:labels,name,' . $label->id],
        ]);

        $label->update([
            'name' => $request->name,
        ]);

        return redirect()->route('labels.index')->with('success', 'Label updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Label $label)
    {
        // Check if label has associated tickets
        if ($label->tickets()->count() > 0) {
            return redirect()->route('labels.index')
                ->with('error', 'Cannot delete label that has associated tickets.');
        }

        $label->delete();

        return redirect()->route('labels.index')->with('success', 'Label deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoteTemplateController extends Controller
{
    public function index()
    {
        $restaurantId = Auth::user()->id;
        $templates = DB::table('note_templates')
            ->where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('restaurant.note-templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100',
            'body'  => 'required|max:500',
        ]);

        DB::table('note_templates')->insert([
            'restaurant_id' => Auth::user()->id,
            'title'         => $request->input('title'),
            'body'          => $request->input('body'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->back()->with('success', 'Şablon eklendi.');
    }

    public function destroy($id)
    {
        DB::table('note_templates')
            ->where('id', $id)
            ->where('restaurant_id', Auth::user()->id)
            ->delete();

        return redirect()->back()->with('success', 'Şablon silindi.');
    }

    public function apiList()
    {
        $restaurantId = Auth::user()->id;
        $templates = DB::table('note_templates')
            ->where('restaurant_id', $restaurantId)
            ->select('id', 'title', 'body')
            ->get();

        return response()->json($templates);
    }
}

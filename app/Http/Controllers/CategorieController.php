<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Categorie::where('status', 'active')
            ->where('restaurant_id', Auth::user()->id)
            ->orderBy('desk', 'asc')
            ->get();

        return view('restaurant.categories.index', compact('categories'));
    }

    public function new()
    {
        return view('restaurant.categories.new');
    }

    public function edit($id)
    {
        $categorie = Categorie::find($id);

        return view('restaurant.categories.edit', compact('categorie'));
    }

    public function create(Request $request)
    {
        $parentAdmin   = Auth::user()->admin_id ? Admin::find(Auth::user()->admin_id) : null;
        $isTestAccount = config('site.test_mode') || ($parentAdmin && $parentAdmin->is_test);
        $testLimit     = ($parentAdmin && $parentAdmin->is_test) ? 2 : config('site.test_mode_limit');

        if ($isTestAccount) {
            if (Categorie::where('restaurant_id', Auth::id())->count() >= $testLimit) {
                return redirect()->back()->with('error', 'Test Hesabı: En Fazla ' . $testLimit . ' Kategori Ekleyebilirsiniz');
            }
        }

        $data = $request->validate([
            'name' => 'required',
        ]);

        $lastDesk = Categorie::where('restaurant_id', Auth::user()->id)->max('desk') ?? 0;

        $create = new Categorie();
        $create->restaurant_id = Auth::user()->id;
        $create->name          = $data['name'];
        $create->desk          = $lastDesk + 1;

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = date('YmdHis') . '-' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/categories'), $filename);
            $create->image = 'upload/categories/' . $filename;
        }

        $create->save();

        return redirect()->back()->with('success', 'Kategori Başarıyla Eklendi.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        $categorie       = Categorie::find($request->id);
        $categorie->name = $data['name'];
        $categorie->desk = $request->desk ?? $categorie->desk;

        if ($request->hasFile('image')) {
            // Eski resmi sil
            if ($categorie->image && file_exists(public_path($categorie->image))) {
                unlink(public_path($categorie->image));
            }
            $file     = $request->file('image');
            $filename = date('YmdHis') . '-' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/categories'), $filename);
            $categorie->image = 'upload/categories/' . $filename;
        }

        if ($request->has('remove_image') && $request->remove_image) {
            if ($categorie->image && file_exists(public_path($categorie->image))) {
                unlink(public_path($categorie->image));
            }
            $categorie->image = null;
        }

        $categorie->save();

        return redirect()->back()->with('success', 'Kategori güncellendi.');
    }

    public function reorder(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $position => $id) {
            Categorie::where('id', (int) $id)
                ->update(['desk' => $position + 1]);
        }
        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $del = Categorie::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    public function index()
    {
        $expenses = Expenses::where('created_by', Auth::id())->where('type','admin')->orderBy('id','desc')->get();
        return view('admin.expenses.index',compact('expenses'));
    }

    public function create()
    {
        return view('admin.expenses.new');
    }

    public function store(Request $request)
    {
        $testMode =config('site.test_mode');

        if ($testMode) {
            if (Expenses::count() > config('site.test_mode_limit')) {
                return redirect()->back()->with('error', 'Test Modu: Üzgünüz, En Fazla '.config('site.test_mode_limit').' Kayıt Ekleyebilirsiniz');
            }
        }
        $create = new Expenses();
        $create->created_by = Auth::user()->id;
        $create->title = $request->input('title');
        $create->type = 'admin';
        $create->expense_type = $request->input('expense_type');
        $create->description = $request->input('description');
        $create->date = $request->input('date');
        $create->payment_method = $request->input('payment_method');
        $create->amount = $request->input('amount');
        $create->save();

        return redirect()->back()->with('success', 'Gider Başarıyla Eklendi.');
    }

    public function edit($id)
    {
        $expense = Expenses::find($id);
        return view('admin.expenses.edit',compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $create = Expenses::find($id);
        $create->title = $request->input('title');
        $create->expense_type = $request->input('expense_type');
        $create->description = $request->input('description');
        $create->date = $request->input('date');
        $create->payment_method = $request->input('payment_method');
        $create->amount = $request->input('amount');
        $create->save();

        return redirect()->back()->with('success', 'Gider Başarıyla Güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $del = Expenses::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}

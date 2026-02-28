<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use App\Models\District;
use App\Models\SuperAdmin;
use App\Models\TopupMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function admin()
    {
        $admins = Admin::where('created_by_id', Auth::guard('dealer')->id())
            ->where('created_by_type', 'dealer')
            ->get();
        return view('dealer.admin.index', compact('admins'));
    }

    public function topup(Request $request, $adminId)
    {
        $admin = Admin::findOrFail($adminId);

        $topupQuery = TopupMovement::where('admin_id', $adminId);

        if ($request->filled('start_date')) {
            $topupQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $topupQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $stats = [
            'current_balance'  => $admin->top_up_balance,
            'total_topup'      => (clone $topupQuery)->sum('top_up'),
            'paid_amount'      => (clone $topupQuery)->where('is_approved', true)->where('is_paid', true)->sum('total_amount'),
            'remaining_amount' => (clone $topupQuery)->where('is_approved', true)->where('is_paid', false)->sum('total_amount'),
        ];

        $recordsQuery = TopupMovement::where('admin_id', $adminId);
        if ($request->filled('start_date')) {
            $recordsQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $recordsQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $records = $recordsQuery->orderBy('created_at', 'desc')->get();

        return view('dealer.admin.topup', compact('admin', 'records', 'stats'));
    }

    public function list(Request $request)
    {
        $status = $request->get('status');

        $query = TopupMovement::query()
            ->where('created_by_id', Auth::guard('dealer')->id())
            ->where('created_by_type', 'dealer');

        if ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'paid') {
            $query->where('is_approved', true)->where('is_paid', true);
        } elseif ($status === 'unpaid') {
            $query->where('is_approved', true)->where('is_paid', false);
        }

        return DataTables::of($query)
            ->addColumn('created_by', function ($row) {
                switch ($row->created_type) {
                    case 'superadmin': return SuperAdmin::find($row->created_by_user_id)?->name ?? '—';
                    case 'admin':      return Admin::find($row->created_by_user_id)?->name ?? '—';
                    case 'dealer':     return User::find($row->created_by_user_id)?->name ?? '—';
                    default:           return '—';
                }
            })
            ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i:s'))
            ->make(true);
    }

    public function approve($recordId)
    {
        $record = TopupMovement::findOrFail($recordId);

        if ($record->is_approved) {
            echo 'OK';
            return;
        }

        $record->is_approved = true;
        $record->save();

        // Admin bakiyesini artır
        $admin = Admin::find($record->admin_id);
        if ($admin) {
            $admin->increment('top_up_balance', $record->top_up);
        }

        // Dealer komisyonunu hesapla ve kaydet
        $dealer = User::find(Auth::guard('dealer')->id());
        if ($dealer) {
            $commission = round($record->total_amount * ($dealer->commission_rate / 100), 2);
            $record->dealer_commission = $commission;
            $record->save();
            $dealer->increment('commission_balance', $commission);
        }

        echo 'OK';
    }

    public function unPaid($recordId)
    {
        $record = TopupMovement::findOrFail($recordId);
        $record->is_paid = false;
        $record->save();
        echo 'OK';
    }

    public function paid($recordId)
    {
        $record = TopupMovement::findOrFail($recordId);
        $record->is_paid = true;
        $record->save();
        echo 'OK';
    }

    public function topUpCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_id'      => 'required',
            'top_up_price'  => 'required|numeric',
            'top_up'        => 'required|integer|min:1',
            'total_amount'  => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        // "₺" karakterini temizle
        $totalAmount = (float) str_replace(['₺', ',', ' '], ['', '.', ''], $request->total_amount);

        TopupMovement::create([
            'admin_id'           => $request->admin_id,
            'top_up_price'       => $request->top_up_price,
            'top_up'             => $request->top_up,
            'type'               => 'yükleme',
            'is_approved'        => 0,
            'total_amount'       => $totalAmount,
            'created_by_user_id' => Auth::guard('dealer')->id(),
            'created_type'       => 'dealer',
        ]);

        return redirect()->back()->with('success', 'Kontör talebi oluşturuldu, onay bekleniyor.');
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::where('id', $id)
            ->where('created_by_id', Auth::guard('dealer')->id())
            ->where('created_by_type', 'dealer')
            ->first();

        if (!$admin) { echo 'ERR'; return; }
        echo $admin->delete() ? 'OK' : 'ERR';
    }

    public function statusAdmin($id)
    {
        $admin = Admin::where('id', $id)
            ->where('created_by_id', Auth::guard('dealer')->id())
            ->where('created_by_type', 'dealer')
            ->first();

        if (!$admin) { echo 'ERR'; return; }
        echo $admin->update(['is_active' => !$admin->is_active]) ? 'OK' : 'ERR';
    }

    public function createAdmin()
    {
        $cities = City::all();
        return view('dealer.admin.create', compact('cities'));
    }

    public function getDistricts($cityId)
    {
        $districts = District::where('city_id', $cityId)->get(['id', 'name']);
        return response()->json($districts);
    }

    public function createAdminRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:admins',
            'phone'       => 'required|unique:admins',
            'password'    => 'required|min:5',
            'lat'         => 'required',
            'lng'         => 'required',
            'city_id'     => 'required',
            'district_id' => 'required',
            'address'     => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        Admin::create([
            'created_by_id'   => Auth::guard('dealer')->id(),
            'created_by_type' => 'dealer',
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'password'        => Hash::make($request->password),
            'latitude'        => $request->lat,
            'longitude'       => $request->lng,
            'city_id'         => $request->city_id,
            'district_id'     => $request->district_id,
            'address'         => $request->address,
        ]);

        return redirect()->back()->with('success', 'Yeni Yönetici Başarıyla Eklendi!');
    }

    public function editAdmin($id)
    {
        $admin = Admin::where('id', $id)
            ->where('created_by_id', Auth::guard('dealer')->id())
            ->where('created_by_type', 'dealer')
            ->firstOrFail();
        $cities = City::all();
        return view('dealer.admin.edit', compact('admin', 'cities'));
    }

    public function updateAdmin($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'lat'         => 'required',
            'lng'         => 'required',
            'city_id'     => 'required',
            'district_id' => 'required',
            'address'     => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $admin = Admin::where('id', $id)
            ->where('created_by_id', Auth::guard('dealer')->id())
            ->where('created_by_type', 'dealer')
            ->firstOrFail();

        $updateData = [
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'latitude'    => $request->lat,
            'longitude'   => $request->lng,
            'city_id'     => $request->city_id,
            'district_id' => $request->district_id,
            'address'     => $request->address,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return redirect()->route('dealer.admin')->with('message', 'Yönetici bilgileri başarıyla güncellendi!');
    }
}

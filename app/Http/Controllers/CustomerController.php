<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        return view('restaurant.customers.index', compact('customers'));
    }

    public function new()
    {
        $admin = Admin::where('id', auth()->user()->admin_id)->first();
        $location = json_encode( ['lat' => floatval($admin->latitude), 'lng' => floatval($admin->longitude)]);
        return view('restaurant.customers.new', compact('location'));
    }

    public function getCustomers()
    {
        try {
            $customers = \App\Models\Customer::where('status','active')->where('restaurant_id', auth()->id())->get();

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteriler yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        $districts = \App\Models\District::where('city_id',auth()->user()->admin->city_id)->get();
        return view('restaurant.customers.edit', compact('customer','districts'));
    }

    public function create(Request $request)
    {
        $parentAdmin   = Auth::user()->admin_id ? Admin::find(Auth::user()->admin_id) : null;
        $isTestAccount = config('site.test_mode') || ($parentAdmin && $parentAdmin->is_test);
        $testLimit     = ($parentAdmin && $parentAdmin->is_test) ? 2 : config('site.test_mode_limit');

        if ($isTestAccount) {
            if (Customer::where('restaurant_id', Auth::id())->count() >= $testLimit) {
                return redirect()->back()->with('test', 'Test Hesabı: En Fazla ' . $testLimit . ' Müşteri Ekleyebilirsiniz');
            }
        }

        if (Customer::where('phone',$request->phone)->where('restaurant_id', Auth::user()->id)->exists()) {
            return redirect()->back()->with('test', 'Bu telefon numarasına ait bir müşteri zaten mevcut!!');
        }

        // Save customer information
        $create = new Customer();
        $create->restaurant_id = Auth::user()->id; // Assuming the authenticated user is the restaurant
        $create->name = $request->input('name');
        $create->phone = $request->input('phone');
        $create->mobile = $request->input('mobile');
        $create->email = $request->input('email')??null;
        $create->save();

        $admin = Admin::find(auth()->user()->admin_id);
        $cityId = $admin->city_id;

        // Check if address data is present
        if ($request->address) {
            foreach ($request->address as $adres) {
                // Save each address for the customer
                $address = new CustomerAddress();
                $address->customer_id = $create->id;
                $address->restaurant_id = Auth::user()->id;
                $address->name = $adres['name'];
                $address->sokak_cadde = $adres['sokak_cadde'];
                $address->bina_no = $adres['bina_no'];
                $address->city_id = $cityId;
                $address->district_id = District::where('name', $adres['ilce'])->first()->id ?? null;
                $address->kat = $adres['kat'];
                $address->latitude = $adres['latitude'];
                $address->longitude =  $adres['longitude'];
                $address->daire_no = $adres['daire_no'];
                $address->mahalle = $adres['mahalle'];
                $address->adres_tarifi = $adres['adres_tarifi'] ?? '';
                $address->save();
            }
        }

        return redirect()->route('restaurant.customers.edit',$create->id)->with('message', 'Müşteri Başarıyla Eklendi.');
    }

    public function update(Request $request)
    {
        $customer = Customer::find($request->id);
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->mobile = $request->mobile;
        $customer->update();

        if ($request->address) {
            // Mevcut adresleri çek
            $existingAddresses = CustomerAddress::where('customer_id', $customer->id)->pluck('id')->toArray();

            // Request’ten gelen id’leri topla
            $requestIds = collect($request->address)->pluck('id')->filter()->toArray();

            // Silinmesi gereken adresler (veritabanında var ama request'te yok)
            $toDelete = array_diff($existingAddresses, $requestIds);
            CustomerAddress::whereIn('id', $toDelete)->delete();

            foreach ($request->address as $adres) {
                if (isset($adres['id']) && $findAddress = CustomerAddress::find($adres['id'])) {
                    // Güncelle
                    $findAddress->update([
                        'name' => $adres['name'],
                        'sokak_cadde' => $adres['sokak_cadde'],
                        'bina_no' => $adres['bina_no'],
                        'kat' => $adres['kat'],
                        'daire_no' => $adres['daire_no'],
                        'district_id' => $adres['district_id'] ?? null,
                        'mahalle' => $adres['mahalle'],
                        'adres_tarifi' => $adres['adres_tarifi'],
                        'latitude' => $adres['latitude'],
                        'longitude' => $adres['longitude'],
                    ]);
                } else {
                    // Yeni ekle
                    CustomerAddress::create([
                        'restaurant_id' => Auth::user()->id,
                        'customer_id' => $customer->id,
                        'district_id' => $adres['district_id'] ?? null,
                        'name' => $adres['name'],
                        'sokak_cadde' => $adres['sokak_cadde'],
                        'bina_no' => $adres['bina_no'],
                        'kat' => $adres['kat'],
                        'daire_no' => $adres['daire_no'],
                        'mahalle' => $adres['mahalle'],
                        'adres_tarifi' => $adres['adres_tarifi'],
                        'latitude' => $adres['latitude'],
                        'longitude' => $adres['longitude'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('message', 'Müşteri ve Adresleri Başarıyla Güncellendi.');
    }

    public function delete($id)
    {

        $customer = Customer::find($id);
        $customer->status = 'deactive';
        $customer->save();

        $customer_address = CustomerAddress::where('customer_id', $id)->get();
        if (count($customer_address) > 0) {
            foreach ($customer_address as $key => $value) {
                $value->status = 'deactive';
                $value->save();
            }
        }

        if ($customer) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}

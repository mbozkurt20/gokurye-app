<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Restaurant;
use App\Services\EntegraService;
use App\Services\VatanSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class MyController extends Controller
{
    public function entegrations()
    {
        $restaurant = Restaurant::find(Auth::user()->id);
        return view('restaurant.entegrations.platforms', compact('restaurant'));
    }
    public function entegrastion_update(Request $request)
    {
        $restaurant = Restaurant::find(Auth::user()->id);
        $platform = $request->input('platform');

        $data = $request->input('data');

        if ($platform == 'gpsyemek') {
            $restaurant->gpsyemek_api_key = $data['api_key'];
            $restaurant->update();
            return redirect()->back()->with('message', 'Entegrasyon Güncellenmesi Tamamlandı.');
        } else {
            $restaurant->$platform = json_encode($data);
        }

        if (!$restaurant->entegra_restaurant_id){
            $businessRes = EntegraService::newBusiness([
                'name' => $restaurant->name,
                'email' => $restaurant->email,
                'password' => $restaurant->name.'.'.$restaurant->code
            ]);

            if ($businessRes->success){
                $restaurantRes = EntegraService::newRestaurant([
                    'name' => $restaurant->name,
                    'businessId' => $businessRes->data->id,
                    'website' => 'https://app.gpskurye.com',
                    'website_restaurant_id' => $restaurant->id,
                ]);

                $restaurant->entegra_restaurant_id = $restaurantRes->data->restaurant_id;
                $restaurant->save();
            }
        }

        $providerRes = EntegraService::patchProvider($restaurant,$platform);

        if ($providerRes['success']){
            $restaurant->update();
            return redirect()->back()->with('message', 'Entegrasyon Güncellenmesi Tamamlandı.');
        }

        return redirect()->back()->with('error', 'Üzgünüz, bir hata meydana geldi, lütfen tekrar deneyiniz.');
    }

    public function paymentEntegrations()
    {
        return view('superadmin.payment');
    }

    public function paymentUpdateEntegrations(REquest $request)
    {
        $this->setEnv([
            'PAYTR_MERCHANT_ID'   => $request->merchant_id,
            'PAYTR_MERCHANT_KEY'  => $request->merchant_key,
            'PAYTR_MERCHANT_SALT' => $request->merchant_salt,
            'PAYTR_SANDBOX'       => $request->sandbox ? 'true' : 'false',
        ]);

        Artisan::call('config:clear');

        return redirect()->back()->with(['message' => 'Bilgiler Güncellendi']);
    }
    public function smsEntegrations()
    {
        $admin = Admin::find(Auth::user()->id);
        return view('admin.entegrations.sms', compact('admin'));
    }

    public function smsEntegrastionUpdate(Request $request)
    {
        $restaurant = Admin::find(Auth::user()->id);
        $restaurant->vatan_sms_customer = $request->vatan_sms_customer;
        $restaurant->vatan_sms_username = $request->vatan_sms_username;;
        $restaurant->vatan_sms_password = $request->vatan_sms_password;;
        $restaurant->vatan_sms_orginator = $request->vatan_sms_orginator;;
        $restaurant->save();

        return redirect()->back()->with('message', 'Sms Entegrasyon Güncellenmesi Tamamlandı.');
    }
    public function smsEntegrastionTest(Request $request)
    {
        $auth = Auth::guard('admin')->user();

        if ($auth->vatan_sms_customer && $auth->vatan_sms_username && $auth->vatan_sms_password && $auth->vatan_sms_orginator){

            try {
                $smsService = new VatanSmsService();
                $result = $smsService->sendSms($request->phone, 'Sayın '.$auth->name.', '.' sms bilgileriniz doğrulanmıştır.'. '\n \n '.
                    'Dilerseniz panelinizden "Aktif Et" diyerek sms göndermeyi aktifleştirebilirsiniz.',$auth->id);

                if($result == "2:Kullanici bulunamadi") {
                    return redirect()->back()->with('test', 'Sms Bilgileriniz Hatalı Görünüyor');
                }
                return redirect()->back()->with('message', 'Sms Gönderildi');
            }catch (\Exception $e){
                return redirect()->back()->with('test', $e->getMessage());
            }
        }else{
            return redirect()->back()->with('test', 'Lütfen gerekli tüm bilgileri giriniz!!');
        }
    }
    public function smsEntegrastionStatus()
    {
        $auth = Auth::guard('admin')->user();
        $auth->is_sms = !$auth->is_sms;

        $auth->update();

        echo "OK";
    }

    public function profile()
    {
        return view('restaurant.profile.profile');
    }
    public function profileUpdate(Request $request)
    {
        $auth = Auth::guard('restaurant')->user();

        if ($request->password){
            $auth->password = Hash::make($request->password);
        }

        if (Restaurant::where('phone',$request->phone)->where('id','!=',$auth->id)->exists()){
            return redirect()->back()->with('test', 'Bu telefon numarası zaten kullanılıyor!!');
        }

        $auth->latitude = $request->input('latitude');
        $auth->longitude = $request->input('longitude');
        $auth->name = $request->input('name');
        $auth->phone = $request->input('phone');
        $auth->update();

        return redirect()->back()->with('message', 'Bilgileriniz Güncellenmiştir.');
    }

    private function setEnv(array $values)
    {
        $envPath = base_path('.env');

        $env = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $env)) {
                $env = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}=\"{$value}\"",
                    $env
                );
            } else {
                $env .= "\n{$key}=\"{$value}\"";
            }
        }

        file_put_contents($envPath, $env);
    }
}

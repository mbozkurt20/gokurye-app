@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-5 px-md-5">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 uppercase leading-none italic">
                SİSTEM <span class="text-indigo-600">ENTEGRASYONLARI</span>
            </h1>
            <div class="flex items-center justify-center gap-2 mt-4">
                <span class="h-[1px] w-12 bg-slate-200"></span>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Gerçek Zamanlı Veri Senkronizasyonu</p>
                <span class="h-[1px] w-12 bg-slate-200"></span>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="max-w-3xl mx-auto mb-8 p-4 bg-white border-l-4 border-emerald-500 shadow-sm rounded-r-2xl flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest m-0">İşlem Başarılı</p>
                        <p class="text-sm font-bold text-slate-700 m-0">{{ session()->get('message') }}</p>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-300 hover:text-slate-500 transition-colors border-0 bg-transparent"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <div class="row g-4">
            @php
                $platforms = [
                    'getir' => ['name' => 'GetirYemek', 'icon' => 'fa-motorcycle', 'color' => 'indigo', 'hex' => '#5d3ebc', 'img' => 'getir.png'],
                    'yemeksepeti' => ['name' => 'Yemeksepeti', 'icon' => 'fa-utensils', 'color' => 'rose', 'hex' => '#fb0050', 'img' => 'yemeksepeti.png'],
                    'trendyol' => ['name' => 'TrendyolYemek', 'icon' => 'fa-bolt', 'color' => 'orange', 'hex' => '#ff6000', 'img' => 'trendyol.png'],
                    'migros' => ['name' => 'MigrosYemek', 'icon' => 'fa-basket-shopping', 'color' => 'amber', 'hex' => '#ff6000', 'img' => 'migros.png']
                ];
            @endphp

            @foreach($platforms as $key => $info)
                @php $val = json_decode($restaurant->$key); @endphp
                <div class="col-xl-4 col-lg-6">
                    <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}" class="h-100">
                        @csrf
                        <input type="hidden" name="platform" value="{{ $key }}">

                        <div class="bg-white !rounded-[45px] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden h-100 flex flex-col group transition-all duration-500 hover:-translate-y-2">

                            <div class="p-8 pb-0 flex items-start justify-between">
                                <div class="w-16 h-16 rounded-[22px] bg-slate-50 flex items-center justify-center border border-slate-100 shadow-sm group-hover:scale-110 transition-transform duration-500">
                                    <img src="{{ asset('theme/images/platforms/' . $info['img']) }}" class="w-10 object-contain  group-hover:grayscale-0 transition-all">
                                </div>
                                <div class="text-right">
                                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-{{$info['color']}}-50 text-{{$info['color']}}-600 border border-{{$info['color']}}-100">
                                        {{ $info['name'] }}
                                    </span>
                                    <div class="mt-2 flex items-center justify-end gap-2">
                                        <span class="w-2 h-2 rounded-full {{ ($val->status ?? true) ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">Live Sync</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8 pt-6 flex-grow">
                                <div class="bg-slate-50/50 rounded-[30px] p-6 mb-6 border border-slate-100/50">
                                    <div class="flex items-center gap-2 mb-4">
                                        <i class="fa-solid fa-key text-[10px] text-slate-400"></i>
                                        <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest">Yetkilendirme</span>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <input type="text" class="w-full !rounded-xl border-slate-200 bg-white/80 p-3.5 font-bold text-slate-600 focus:ring-4 focus:ring-{{$info['color']}}-100 focus:border-{{$info['color']}}-300 transition-all border text-xs"
                                                   name="data[information][restaurantId]" value="{{ $val->information->restaurantId ?? '' }}" placeholder="Restaurant ID" required>
                                        </div>

                                        @if($key == 'getir')
                                            <input type="text" class="w-full !rounded-xl border-slate-200 bg-white/80 p-3.5 font-bold text-slate-600 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition-all border text-xs"
                                                   name="data[information][secretKey]" value="{{ $val->information->secretKey ?? '' }}" placeholder="Secret Key" required>
                                        @elseif($key == 'yemeksepeti' || $key == 'migros')
                                            <input type="text" class="w-full !rounded-xl border-slate-200 bg-white/80 p-3.5 font-bold text-slate-600 focus:ring-4 focus:ring-{{$info['color']}}-100 focus:border-{{$info['color']}}-300 transition-all border text-xs"
                                                   name="data[information][chainId]" value="{{ $val->information->chainId ?? '' }}" placeholder="Chain ID" required>
                                            @if($key == 'migros')
                                                <input type="text" class="w-full !rounded-xl border-slate-200 bg-white/80 p-3.5 font-bold text-slate-600 mt-3 focus:ring-4 focus:ring-amber-100 focus:border-amber-300 transition-all border text-xs"
                                                       name="data[information][apiKey]" value="{{ $val->information->apiKey ?? '' }}" placeholder="API Key" required>
                                            @endif
                                        @elseif($key == 'trendyol')
                                            <input type="text" class="w-full !rounded-xl border-slate-200 bg-white/80 p-3.5 font-bold text-slate-600 focus:ring-4 focus:ring-orange-100 focus:border-orange-300 transition-all border text-xs"
                                                   name="data[information][supplierId]" value="{{ $val->information->supplierId ?? '' }}" placeholder="Supplier ID" required>
                                            <input type="password" class="w-full !rounded-xl border-slate-200 bg-white/80 p-3.5 font-bold text-slate-600 mt-3 focus:ring-4 focus:ring-orange-100 focus:border-orange-300 transition-all border text-xs"
                                                   name="data[information][apiSecretKey]" value="{{ $val->information->apiSecretKey ?? '' }}" placeholder="API Secret Key" required>
                                        @endif
                                    </div>
                                </div>

                                <div class="px-2 mb-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center gap-4">
                                            <div class="form-switch">
                                                <input class="form-check-input !w-12 !h-6 !cursor-pointer" type="checkbox" name="data[status]" value="true" id="st_{{$key}}" {{ ($val->status ?? true) ? 'checked' : '' }}>
                                            </div>
                                            <label class="text-[11px] font-black text-slate-700 uppercase cursor-pointer" for="st_{{$key}}">Mağaza Aktif</label>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div class="form-switch">
                                                <input class="form-check-input !w-12 !h-6 !cursor-pointer" type="checkbox" name="data[otomatikOnay]" value="true" id="auto_{{$key}}" {{ ($val->otomatikOnay ?? false) ? 'checked' : '' }}>
                                            </div>
                                            <label class="text-[11px] font-black text-slate-700 uppercase cursor-pointer" for="auto_{{$key}}">Oto-Onay</label>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="col-span-2">
                                            <div class="flex justify-between mb-2">
                                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Hazırlanma Süresi</label>
                                                <span id="val_{{$key}}" class="px-3 py-1 rounded-lg bg-{{$info['color']}}-50 text-{{$info['color']}}-600 text-[10px] font-black tracking-tighter italic border border-{{$info['color']}}-100">
            {{ $val->service ?? 30 }} Dakika
        </span>
                                            </div>
                                            <input type="range"
                                                   class="form-range custom-range"
                                                   min="10"
                                                   max="90"
                                                   step="5"
                                                   name="data[service]"
                                                   value="{{ $val->service ?? 30 }}"
                                                   oninput="document.getElementById('val_{{$key}}').innerHTML = this.value + ' Dakika'">
                                        </div>
                                        <div class="mt-2">
                                            <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block">Zil Notu</label>
                                            <input type="text" class="w-full border-b-2 border-slate-100 bg-transparent py-2 font-bold text-slate-600 text-[11px] focus:border-indigo-500 transition-all outline-none" name="data[doNotKnock]" value="{{ $val->doNotKnock ?? 'Zil Çalma' }}">
                                        </div>
                                        <div class="mt-2">
                                            <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block">Temassız Notu</label>
                                            <input type="text" class="w-full border-b-2 border-slate-100 bg-transparent py-2 font-bold text-slate-600 text-[11px] focus:border-indigo-500 transition-all outline-none" name="data[dropOffAtDoor]" value="{{ $val->dropOffAtDoor ?? 'Temassız' }}">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="w-full !rounded-[24px] py-4 font-black text-[11px] uppercase tracking-[0.3em] transition-all hover:shadow-2xl hover:shadow-{{$info['color']}}-200/50 border-0 flex items-center justify-center gap-3 active:scale-95"
                                        style="background: {{ $info['hex'] }}; color: white;">
                                    <i class="fa-solid fa-arrows-rotate animate-spin-slow"></i>
                                    Ayarları Güncelle
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .animate-spin-slow { animation: spin 4s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        /* Custom Checkbox/Switch */
        .form-check-input:checked { background-color: #4f46e5 !important; border-color: #4f46e5 !important; }

        /* Custom Range Slider */
        .custom-range::-webkit-slider-runnable-track { background: #f1f5f9; height: 6px; border-radius: 10px; }
        .custom-range::-webkit-slider-thumb { background: #4f46e5; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); height: 18px; width: 18px; margin-top: -6px; border-radius: 50%; -webkit-appearance: none; }

        .row { --bs-gutter-x: 2rem; --bs-gutter-y: 2rem; }
    </style>
@endsection

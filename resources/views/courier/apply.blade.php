<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Kurye Başvurusu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .pattern-bg {
            background-color: #4338ca;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M92.66 7.38c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

<div class="pattern-bg pt-20 pb-32 px-6">
    <div class="max-w-3xl mx-auto text-center">
        <a href="{{ route('admin.login') }}">
            <img src="{{config('site.logo')}}" alt="Logo" class="h-12 mx-auto mb-8 brightness-0 invert opacity-90">
        </a>
        <h1 class="text-5xl font-black text-white tracking-tight mb-4 leading-tight">Ekibimize Katılın</h1>
        <p class="text-indigo-100/70 font-bold text-lg max-w-xl mx-auto">Siz de lojistiğin geleceğinde yerinizi alın.
            Formu doldurun, hemen değerlendirelim.</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-6 -mt-20 pb-20">

    @if(session('success'))
        <div
            class="mb-8 p-6 bg-emerald-500 text-white rounded-[32px] shadow-xl shadow-emerald-200 flex items-center gap-5">
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-xl"></i>
            </div>
            <div>
                <p class="font-black text-sm uppercase tracking-wider">Başvurunuz Alındı!</p>
                <p class="text-emerald-50 text-xs font-bold mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div
            class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 p-2 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Hata Oluştu</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight">
                        {{ session()->get('error') }}
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()"
                        class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('courier.apply.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
            <div class="flex items-center gap-4 mb-10">
                <div
                    class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800 tracking-tight">Kişisel Bilgiler</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kimlik ve iletişim
                        detayları</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Ad Soyad</label>
                    <input required type="text" name="name" value="{{ old('name') }}"
                           class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all"
                           placeholder="Jone Doe">
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Telefon</label>
                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => old('phone'), 'class' => 'w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all' ])
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">TC Kimlik
                        No</label>
                    <input required type="number" name="tc_id" maxlength="11" value="{{ old('tc_id') }}"
                           class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all"
                           placeholder="00000000000">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Yaş</label>
                        <input required type="number" name="age" min="18" max="70" value="{{ old('age') }}"
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all"
                               placeholder="25">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Kan
                            Grubu</label>
                        <select required name="blood_type"
                                class="w-full px-4 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-600 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all text-sm">
                            <option value="">Seç</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','0+','0-'] as $bt)
                                <option
                                    value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 text-indigo-600">Yönetici
                        Kodu</label>
                    <input required type="text" name="admin_code" value="{{ old('admin_code') }}"
                           class="w-full px-6 py-4 bg-indigo-50/50 border-2 border-indigo-100 rounded-2xl font-black text-indigo-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all placeholder:text-indigo-200"
                           placeholder="ADM-XXXX">
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Profil
                        Fotoğrafı</label>
                    <div class="relative group">
                        <input required type="file" name="profile_photo" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl font-bold text-slate-400 group-hover:border-indigo-400 transition-all flex items-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span class="text-xs uppercase tracking-tighter">Dosya Seçin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-orange-100">
                        <i class="fas fa-motorcycle text-sm"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Araç</h3>
                </div>
                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Araç Tipi</label>
                        <select required name="vehicle_type"
                                class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-600 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all text-sm">
                            <option value="">Seçiniz</option>
                            <option value="motor" {{ old('vehicle_type') == 'motor' ? 'selected' : '' }}>Motosiklet
                            </option>
                            <option value="otomobil" {{ old('vehicle_type') == 'otomobil' ? 'selected' : '' }}>
                                Otomobil
                            </option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Plaka No</label>
                        <input required type="text" name="plate" value="{{ old('plate') }}"
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all uppercase"
                               placeholder="34 ABC 123">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                        <i class="fas fa-wallet text-sm"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Ödeme</h3>
                </div>
                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Banka Adı</label>
                        <input required type="text" name="bank" value="{{ old('bank') }}"
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all"
                               placeholder="Ziraat Bankası">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">IBAN</label>
                        <input required type="text" name="iban" maxlength="32" value="{{ old('iban') }}"
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all"
                               placeholder="TR00 0000...">
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit"
                    class="w-full py-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[24px] font-black text-xs uppercase tracking-[0.3em] shadow-2xl shadow-indigo-200 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                <i class="fas fa-paper-plane text-sm"></i>
                BAŞVURUYU TAMAMLA
            </button>
        </div>

        <div class="text-center pt-6">
            <a href="{{ route('admin.login') }}"
               class="inline-flex items-center gap-2 text-[11px] font-black text-slate-400 hover:text-indigo-600 uppercase tracking-[0.2em] transition-colors group">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Vazgeç ve Geri Dön
            </a>
        </div>
    </form>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Kurye Başvurusu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pattern-bg {
            background-color: #4f46e5;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M92.66 7.38c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

{{-- Header --}}
<div class="pattern-bg py-12 px-6">
    <div class="max-w-2xl mx-auto text-center">
        <img src="{{config('site.logo')}}" alt="Logo" class="h-20 mx-auto mb-6 brightness-0 invert">
        <h1 class="text-4xl font-black text-white tracking-tight mb-3">Kurye Başvurusu</h1>
        <p class="text-indigo-200 font-bold text-sm">Ekibimize katılmak için aşağıdaki formu doldurun. Yöneticimiz en kısa sürede sizinle iletişime geçecektir.</p>
    </div>
</div>

<div class="max-w-2xl mx-auto px-6 py-12">

    @if(session('success'))
        <div class="mb-8 p-5 bg-emerald-50 border border-emerald-200 rounded-3xl flex items-start gap-4">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="font-black text-emerald-800 text-sm">Başvurunuz Alındı!</p>
                <p class="text-emerald-700 text-xs font-bold mt-1">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 p-5 bg-rose-50 border border-rose-200 rounded-3xl flex items-start gap-4">
            <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <p class="text-rose-700 text-sm font-bold pt-2">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('courier.apply.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Kişisel Bilgiler --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Kişisel Bilgiler</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Ad Soyad *</label>
                    <input required type="text" name="name" value="{{ old('name') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="Ad Soyad">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Telefon *</label>
                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => old('phone'), 'class' => 'w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100' ])
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">TC Kimlik No *</label>
                    <input required type="number" name="tc_id" maxlength="11" value="{{ old('tc_id') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="12345678901">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Yaş *</label>
                    <input required type="number" name="age" min="18" max="70" maxlength="2" value="{{ old('age') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="25">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Kan Grubu *</label>
                    <select required name="blood_type" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-600 focus:outline-none focus:border-indigo-400">
                        <option value="">Seçiniz</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','0+','0-'] as $bt)
                            <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Uygulama Şifresi *</label>
                    <input required type="password" name="password"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="Mobil uygulama için şifre">
                </div>
            </div>

            <div class="mt-4">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Profil Fotoğrafı *</label>
                <input required type="file" name="profile_photo" accept="image/*"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400">
            </div>
        </div>

        {{-- Araç Bilgileri --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-motorcycle text-sm"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Araç Bilgileri</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Araç Tipi *</label>
                    <select required name="vehicle_type" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-600 focus:outline-none focus:border-indigo-400">
                        <option value="">Seçiniz</option>
                        <option value="motor" {{ old('vehicle_type') == 'motor' ? 'selected' : '' }}>Motor</option>
                        <option value="otomobil" {{ old('vehicle_type') == 'otomobil' ? 'selected' : '' }}>Otomobil</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Plaka *</label>
                    <input required type="text" name="plate" value="{{ old('plate') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="34 ABC 123">
                </div>
            </div>
        </div>

        {{-- Banka Bilgileri --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-university text-sm"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Banka Bilgileri</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Banka *</label>
                    <input required type="text" name="bank" value="{{ old('bank') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="Ziraat Bankası">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">IBAN *</label>
                    <input required type="text" name="iban" maxlength="32" value="{{ old('iban') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="TR00 0000 0000 0000 0000 0000 00">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-200 transition-all active:scale-[0.98]">
            <i class="fas fa-paper-plane mr-2"></i> BAŞVURUYU GÖNDER
        </button>

        <div class="text-center">
            <a href="{{ route('admin.login') }}" class="text-[11px] font-bold text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition-colors">
                ← Giriş Sayfasına Dön
            </a>
        </div>
    </form>
</div>

</body>
</html>

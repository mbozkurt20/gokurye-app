@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        @if(session()->has('success'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-4 animate-bounce-short">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-50 p-2 rounded-xl text-indigo-600"><i class="fas fa-check-circle"></i></div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase">BAŞARILI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out">
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
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    GİDER <span class="text-slate-400">DÜZENLE</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    #{{ $expense->id }} numaralı harcama kaydını güncelliyorsunuz.
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-4 py-2 !rounded-2xl m-0 shadow-sm border border-slate-50">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="/admin/expenses" class="text-slate-400">Giderler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active">Düzenle</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-xl-9 col-lg-12 ">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 md:p-12">
                        <div class="flex items-center gap-3 mb-10">
                            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-edit text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Kayıt Bilgilerini Güncelle</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Yapılan değişiklikler finansal raporları anlık etkiler.</p>
                            </div>
                        </div>

                        <form method="post" action="{{route('admin.expenses.update', $expense->id)}}">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Gider Başlığı</label>
                                    <div class="relative">
                                        <i class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                                        <input value="{{$expense->title}}" required type="text" class="form-control !rounded-2xl !py-3.5 !pl-10 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 transition-all shadow-sm" name="title" placeholder="Gider Başlığı">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <x-money-input
                                        name="amount"
                                        label="Tutar Giriniz "
                                        required="true"
                                        value="{{$expense->amount}}"
                                    />
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Ödeme Tarihi</label>
                                    <input value="{{ date('Y-m-d\TH:i', strtotime($expense->date)) }}" required type="datetime-local" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 shadow-sm" name="date">
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Ödeme Yöntemi</label>
                                    <select required class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600 shadow-sm" name="payment_method">
                                        <option {{$expense->payment_method == 'Nakit' ? 'selected' : ''}} value="Nakit">Nakit</option>
                                        <option {{$expense->payment_method == 'Kredi Kartı' ? 'selected' : ''}} value="Kredi Kartı">Kredi Kartı</option>
                                        <option {{$expense->payment_method == 'Eft/Havale' ? 'selected' : ''}} value="Eft/Havale">Eft/Havale</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Gider Türü</label>
                                    <select required class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600 shadow-sm" name="expense_type">
                                        <option {{$expense->expense_type == 'Kira' ? 'selected' : ''}} value="Kira">Kira</option>
                                        <option {{$expense->expense_type == 'Fatura' ? 'selected' : ''}} value="Fatura">Fatura</option>
                                        <option {{$expense->expense_type == 'Personel' ? 'selected' : ''}} value="Personel">Personel</option>
                                        <option {{$expense->expense_type == 'Malzeme' ? 'selected' : ''}} value="Malzeme">Malzeme</option>
                                        <option {{$expense->expense_type == 'Diğer' ? 'selected' : ''}} value="Diğer">Diğer</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Açıklama</label>
                                    <textarea class="form-control !rounded-[24px] border-slate-100 font-bold text-slate-700 p-4 shadow-sm" name="description" rows="4" placeholder="Detaylı açıklama...">{{$expense->description}}</textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 mt-12 pt-8 border-t border-slate-50">
                                <a href="/admin/expenses" class="px-8 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all">Vazgeç</a>
                                <button type="submit" class="bg-indigo-600 text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-900/20 hover:scale-[1.02] transition-all">
                                    <i class="fas fa-sync me-2"></i> DEĞİŞİKLİKLERİ KAYDET
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

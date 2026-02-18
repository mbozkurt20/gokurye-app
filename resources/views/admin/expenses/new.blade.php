@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    YENİ <span class="text-slate-400">GİDER KAYDI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    İşletme harcamalarınızı ve ödemelerinizi buradan kayıt altına alabilirsiniz.
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-4 py-2 !rounded-2xl m-0 shadow-sm border border-slate-50">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="/admin/expenses" class="text-slate-400">Giderler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active">Yeni Ekle</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-xl-9 col-lg-12 mx-auto">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 md:p-12">
                        <div class="flex items-center gap-3 mb-10">
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Harcama Detayları</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Lütfen zorunlu alanları eksiksiz doldurun.</p>
                            </div>
                        </div>

                        <form method="post" action="{{route('admin.expenses.store')}}">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Gider Başlığı</label>
                                    <div class="relative">
                                        <i class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                                        <input required type="text" class="form-control !rounded-2xl !py-3.5 !pl-10 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 transition-all shadow-sm" name="title" placeholder="Örn: Ofis Kirası">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Tutar (₺)</label>
                                    <div class="relative">
                                        <i class="fas fa-lira-sign absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                                        <input required type="number" step="0.01" class="form-control !rounded-2xl !py-3.5 !pl-10 border-slate-100 font-bold text-slate-700 shadow-sm" name="amount" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Ödeme Tarihi</label>
                                    <input required value="{{date('Y-m-d\TH:i', strtotime(\Carbon\Carbon::now()))}}" type="datetime-local" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 shadow-sm" name="date">
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Ödeme Yöntemi</label>
                                    <select required class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600 shadow-sm" name="payment_method">
                                        <option selected value="Nakit">Nakit</option>
                                        <option value="Kredi Kartı">Kredi Kartı</option>
                                        <option value="Eft/Havale">Eft/Havale</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Gider Türü</label>
                                    <select required class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600 shadow-sm" name="expense_type">
                                        <option value="Kira">Kira</option>
                                        <option selected value="Fatura">Fatura</option>
                                        <option value="Personel">Personel</option>
                                        <option value="Malzeme">Malzeme</option>
                                        <option value="Diğer">Diğer</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Açıklama <span class="text-slate-300 normal-case italic">(Opsiyonel)</span></label>
                                    <textarea class="form-control !rounded-[24px] border-slate-100 font-bold text-slate-700 p-4 shadow-sm" name="description" rows="4" placeholder="Harcamaya dair notlarınızı buraya yazabilirsiniz..."></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 mt-12 pt-8 border-t border-slate-50">
                                <a href="/admin/expenses" class="px-8 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all">İptal</a>
                                <button type="submit" class="bg-indigo-600 text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-900/20 hover:scale-[1.02] transition-all">
                                    <i class="fas fa-save me-2"></i> KAYDI TAMAMLA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

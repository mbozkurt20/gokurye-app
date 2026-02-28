@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Giderler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Giderler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
                </ol>
            </div>
        </div>
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

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Gider Ekle</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('expenses.store')}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Gider Başlığı</label>
                                        <input required type="text" class="form-control" name="title" placeholder="Gider Başlığı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <x-money-input
                                            name="amount"
                                            label="Gider Tutarı"
                                            required="true"
                                        />
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ödeme Tarihi</label>
                                        <input required type="datetime-local" class="form-control" name="date" >
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ödeme Türü</label>
                                        <select required class="form-control" name="payment_method" id="">
                                            <option selected value="Nakit">Nakit</option>
                                            <option value="Kredi Kartı">Kredi Kartı</option>
                                            <option value="Eft/Havale">Eft/Havale</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Gider Türü</label>
                                        <select required class="form-control" name="expense_type" id="">
                                            <option value="Kira">Kira</option>
                                            <option selected value="Fatura">Fatura</option>
                                            <option value="Personel">Personel</option>
                                            <option value="Malzeme">Malzeme</option>
                                            <option value="Diğer">Diğer</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Açıklama (opsiyonel)</label>
                                        <textarea class="form-control" name="description" placeholder="Eklemek istediğiniz detaylı açıklama"></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="special-button float-end mt-4">Kaydet</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



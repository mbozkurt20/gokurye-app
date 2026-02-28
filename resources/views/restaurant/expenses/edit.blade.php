@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Giderler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Giderler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Düzenle</a></li>
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
                        <h4 class="card-title">Gider Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('expenses.update',$expense->id)}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Gider Başlığı</label>
                                        <input value="{{$expense->title}}" required type="text" class="form-control" name="title" placeholder="Gider Başlığı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <x-money-input
                                            name="amount"
                                            label="Gider Tutarı Giriniz"
                                            required="true"
                                            value="{{$expense->amount}}"
                                        />
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ödeme Tarihi</label>
                                        <input value="{{$expense->date}}" required type="datetime-local" class="form-control" name="date" >
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ödeme Method</label>
                                        <select required class="form-control" name="payment_method" id="">
                                            <option {{$expense->payment_method == 'Nakit' ? 'selected' : null}} value="Nakit">Nakit</option>
                                            <option {{$expense->payment_method == 'Kredi Kartı' ? 'selected' : null}} value="Kredi Kartı">Kredi Kartı</option>
                                            <option {{$expense->payment_method == 'Eft/Havale' ? 'selected' : null}} value="Eft/Havale">Eft/Havale</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Gider Türü</label>
                                        <select required class="form-control" name="expense_type" id="">
                                            <option {{$expense->expense_type == 'Kira' ? 'selected' : null}} value="Kira">Kira</option>
                                            <option {{$expense->expense_type == 'Fatura' ? 'selected' : null}} selected value="Fatura">Fatura</option>
                                            <option {{$expense->expense_type == 'Personel' ? 'selected' : null}} value="Personel">Personel</option>
                                            <option {{$expense->expense_type == 'Malzeme' ? 'selected' : null}} value="Malzeme">Malzeme</option>
                                            <option {{$expense->expense_type == 'Diğer' ? 'selected' : null}} value="Diğer">Diğer</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Açıklama (opsiyonel)</label>
                                        <textarea class="form-control" name="description" placeholder="Eklemek istediğiniz detaylı açıklama">{{$expense->description}}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="special-button float-end mt-4">Kaydı Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection



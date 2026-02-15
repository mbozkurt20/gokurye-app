@extends('admin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/reports/index.css')}}">

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kontör Bakiyesi</h2>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="card card-body ">
                <div class="" style="max-width: 35vw">
                    <p class="fw-bold size-2 text-dark">
                        Merhaba, {{\Illuminate\Support\Facades\Auth::guard('admin')->user()->name}}</p>

                    <p class="size-2 special-button mb-5 rounded-lg">Güncel Kontör
                        Bakiyeniz: {{\Illuminate\Support\Facades\Auth::guard('admin')->user()->top_up_balance}}</p>

                    <p class="text-primary">
                        <strong> Bilgilendirme:: </strong> Kontör bakiyesini buradan ve üst alandan kontrol
                        edebilirsiniz.
                    </p>

                    <p class="text-danger">
                        <strong>Dikkat:: Kontör bakiyeniz yetersiz olması durumunda siparişleriniz eklenmeyecektir, bu
                            durumdan {{config('site.name')}} olarak sorumluluk almadığımızı belirtmek isteriz.</strong>
                    </p>

                   {{--  <p class="text-success">
                        <strong> Satın Alım:: </strong> Paketlerimizi
                        <a class="text-primary" style="text-decoration: underline"
                           href="https://gpskurye.com/fiyat/">{{config('site.name')}} </a>
                        adresimizden inceleyebilirsiniz.
                    </p> --}}
                </div>

                <!-- tami için route('admin.payment.tami.form') GET yap paytr için POST route('payment.paytr.form')  -->
              @if(auth()->check() && !auth()->user()->phone)
                    <p class="py-2 font-weight-bold">Bakiye yüklemek için lütfen telefon numaranızı doğrulayınız.</p>
                    <a class="btn btn-spotify w-25" href="/admin/profile">Profilime Git</a>
                @else
                    <div class="border border-dark p-3 rounded-2" style="max-width: 25%">
                        <form method="POST" action="{{ route('admin.payment.paytr.form') }}">
                            @csrf

                            <!-- Kullanıcıya tanımlı kontör fiyatı -->
                            <input type="hidden" id="top_up_price" value="{{ auth()->user()->top_up_price }}">

                            <div class="mb-3">
                                <label class="fw-bold text-black">Kontör Fiyatı</label>
                                <div class="form-control bg-secondary text-white fw-bold">
                                    1 Kontör = <span class="text-white fw-bold">{{ auth()->user()->top_up_price }} ₺</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-black">Kontör Adet</label>
                                <input required class="form-control" type="number" min="1" placeholder="Adet giriniz"
                                       id="top_up" name="top_up">
                            </div>

                            <!-- Anlık hesaplanan tutar -->
                            <div class="alert alert-info" id="hesaplamaBox" style="display: none;">
                                <span class="fw-bold">Ödemeniz Gereken Tutar:</span>
                                <span id="toplamTutar" class="text-success"></span> ₺
                            </div>

                            <!-- Butonlar -->
                            <div class="d-flex gap-2">
                                <button class="special-button" style="display: none" type="button" id="hesaplaBtn">Hesapla
                                </button>
                                <button class="special-button" type="submit">Ödeme Yap</button>
                            </div>
                        </form>
                    </div>
                @endif
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">Kontör Hareketleri</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead style="background-color: #259a38; color: #fff;">
                            <tr>
                                <th>#</th>
                                <th>İşlem Sahibi</th>
                                <th>Kontör Fiyatı</th>
                                <th>Adet</th>
                                <th>Toplam Tutar</th>
                                <th>Tip</th>
                                <th>Onay Durumu</th>
                                <th>Ödeme Durumu</th>
                                <th>Ödeme Detayları</th>
                                <th>Tarih</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($movements as $key => $movement)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $movement->created_type == 'admin' ? 'Siz' : 'Yönetici' }}</td>
                                    <td>{{ $movement->top_up_price }} ₺</td>
                                    <td>{{ $movement->top_up }}</td>
                                    <td>{{ $movement->total_amount }} ₺</td>
                                    <td>{{ ucfirst($movement->type) }}</td>

                                    <td>
                                        @if($movement->is_approved)
                                            <span class="badge" style="background-color:#259a38;">Onaylı</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Bekliyor</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movement->is_paid)
                                            <span class="badge" style="background-color:#259a38;">Ödendi</span>
                                        @else
                                            <span class="badge bg-danger">Ödenmedi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movement->payment_details)
                                            <button class="btn btn-sm" style="background-color:#259a38; color:#fff;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#paymentDetailsModal{{ $movement->id }}">
                                                Görüntüle
                                            </button>

                                            <!-- Modal -->
                                          <div class="modal fade" data-bs-backdrop="false" id="paymentDetailsModal{{ $movement->id }}"
                                                 tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header"
                                                             style="background-color:#259a38; color:#fff;">
                                                            <h5 class="modal-title text-white">Ödeme Detayları</h5>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Kapat"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @php
                                                                $details = json_decode($movement->payment_details, true);
                                                            @endphp

                                                            @if(is_array($details))
                                                                <ul class="list-group">
                                                                    @foreach($details as $key => $value)
                                                                        @if($key=='card')
                                                                            <li class="list-group-item d-flex justify-content-between"
                                                                                style="border-left:5px solid #259a38;">
                                                                                <strong>Kart Tipi</strong>
                                                                                <span>{{ $value['cardType'] }}</span>
                                                                            </li>

                                                                            <li class="list-group-item d-flex justify-content-between"
                                                                                style="border-left:5px solid #259a38;">
                                                                                <strong>BIN Number</strong>
                                                                                <span>{{ $value['binNumber'] }}</span>
                                                                            </li>

                                                                            <li class="list-group-item d-flex justify-content-between"
                                                                                style="border-left:5px solid #259a38;">
                                                                                <strong>Banka</strong>
                                                                                <span>{{ $value['cardBrand'] }}</span>
                                                                            </li>

                                                                            <li class="list-group-item d-flex justify-content-between"
                                                                                style="border-left:5px solid #259a38;">
                                                                                <strong>Kart Organizasyonu</strong>
                                                                                <span>{{ $value['cardOrganization'] }}</span>
                                                                            </li>
                                                                        @else

                                                                            @switch($key)
                                                                                @case('is3D')
                                                                                    <li class="list-group-item d-flex justify-content-between"
                                                                                        style="border-left:5px solid #259a38;">
                                                                                        <strong>3D Ödeme</strong>
                                                                                        <span>{{ $value ? 'Evet' : 'Hayır' }}</span>
                                                                                    </li>
                                                                                    @break

                                                                                @case('currency')
                                                                                    <li class="list-group-item d-flex justify-content-between"
                                                                                        style="border-left:5px solid #259a38;">
                                                                                        <strong>Birim</strong>
                                                                                        <span>{{ $value }}</span>
                                                                                    </li>
                                                                                    @break
                                                                                @case('currency')
                                                                                    <li class="list-group-item d-flex justify-content-between"
                                                                                        style="border-left:5px solid #259a38;">
                                                                                        <strong>Birim</strong>
                                                                                        <span>{{ $value }}</span>
                                                                                    </li>
                                                                                    @break
                                                                                @case('amount')
                                                                                    <li class="list-group-item d-flex justify-content-between"
                                                                                        style="border-left:5px solid #259a38;">
                                                                                        <strong>Toplam Tutar</strong>
                                                                                        <span>{{ $value }}₺</span>
                                                                                    </li>
                                                                                    @break
                                                                            @endswitch

                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p class="text-muted">Detay çözümlenemedi.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">Henüz hareket bulunmamaktadır.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <script>
        const axios = require('axios');

        async function sendWhatsAppMessage() {
            const token = "BURAYA_META_ACCESS_TOKEN";
            const phone_number_id = "08503469503";

            await axios.post(`https://graph.facebook.com/v17.0/${phone_number_id}/messages`, {
                messaging_product: "whatsapp",
                to: "905xxxxxxxxx", // Numaranız (ülke kodu ile)
                type: "text",
                text: {body: "Merhaba! Bu bir test mesajıdır."}
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            console.log("Mesaj gönderildi!");
        }
    </script>

    <script>
        const topUpInput = document.getElementById("top_up");
        const fiyat = parseFloat(document.getElementById("top_up_price").value);
        const hesaplamaBox = document.getElementById("hesaplamaBox");
        const toplamTutar = document.getElementById("toplamTutar");
        const hesaplaBtn = document.getElementById("hesaplaBtn");

        function hesapla() {
            let adet = parseInt(topUpInput.value);
            let toplam = adet * fiyat;

            if (!isNaN(toplam) && adet > 0) {
                toplamTutar.innerText = toplam.toFixed(2);
                hesaplamaBox.style.display = "block";
            } else {
                hesaplamaBox.style.display = "none";
            }
        }

        // Hesapla butonu ile
        hesaplaBtn.addEventListener("click", hesapla);

        // Kullanıcı yazdıkça otomatik hesapla
        topUpInput.addEventListener("input", hesapla);
    </script>
@endsection



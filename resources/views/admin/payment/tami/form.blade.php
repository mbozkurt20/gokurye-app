@extends('admin.layouts.app')

@section('content')
    <div class="container py-5 d-flex justify-content-center">

        {{-- Başarılı veya Hata Mesajları --}}
        @if(session()->has('error'))
            <div class="alert alert-success alert-dismissible fade show w-100 max-w-md" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show w-100 max-w-md" role="alert">
                {{ session()->get('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Ödeme Kartı Formu --}}
        <div class="card shadow-sm w-100" style="max-width: 450px;">
            <div class="card-body">
                {{-- Kart Görseli --}}
                <div class="bg-success text-white rounded-3 p-3 mb-4 position-relative" style="height: 180px;">
                    <img src="/theme/images/Tami-bulten.png" alt="Garanti" class="position-absolute top-0 end-0 m-3" style="height: 30px;">

                    <div class="bg-warning rounded-2" style="width:50px; height:36px; margin-bottom: 16px;"></div>

                    <h5 class="mt-4" id="card_number_display">**** **** **** 7014</h5>

                    <div class="d-flex justify-content-between align-items-end mt-4">
                        <small id="card_expire_display">{{ now()->format('m/y') }}</small>
                        <small id="card_name_display">Ad Soyad</small>
                    </div>
                </div>

                {{-- Ödeme Formu --}}
                <form method="POST" action="{{ route('payment.start') }}">
                    @csrf
                    <input type="hidden" name="topup" value="{{ $topup }}">
                    <input style="display: none" type="number" step="0.01" class="form-control"  id="amount" name="amount" value="{{$amount}}">

                    <div class="mb-3">
                        <label for="card_name" class="form-label">Kart Üzerindeki İsim</label>
                        <input  required type="text" class="form-control" id="card_name" name="card_name" value="">
                    </div>

                    <div class="mb-3">
                        <label for="card_number" class="form-label">Kart Numarası</label>
                        <input required type="text" class="form-control" id="card_number" name="card_number" value="" maxlength="16">
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="expire_month" class="form-label">Ay</label>
                            <input required type="text" class="form-control text-center" id="expire_month" name="expire_month" value="" maxlength="2">
                        </div>
                        <div class="col">
                            <label for="expire_year" class="form-label">Yıl</label>
                            <input required type="text" class="form-control text-center" id="expire_year" name="expire_year" value="" maxlength="4">
                        </div>
                        <div class="col">
                            <label for="cvc" class="form-label">CVC</label>
                            <input type="password" class="form-control text-center" id="cvc" name="cvc" maxlength="4" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Kontör Adedi</label>
                        <h5>{{ $topup }} Paket</h5>
                    </div>


                    <div class="mb-3">
                        <label for="amount" class="form-label">Tutar (₺)</label>
                        <h5>{{ $amount }}₺</h5>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mb-2">Ödeme Yap</button>
                    <p class="text-center text-muted small">Ödeme işlemi 256-bit SSL ile korunmaktadır.</p>
                </form>
            </div>
        </div>
    </div>

    <script>
        const cardNumberInput = document.getElementById('card_number');
        const cardNameInput = document.getElementById('card_name');
        const expireMonthInput = document.getElementById('expire_month');
        const expireYearInput = document.getElementById('expire_year');

        const cardNumberDisplay = document.getElementById('card_number_display');
        const cardNameDisplay = document.getElementById('card_name_display');
        const cardExpireDisplay = document.getElementById('card_expire_display');

        cardNumberInput.addEventListener('input', () => {
            let value = cardNumberInput.value.replace(/\D/g,''); // sadece rakam
            if(value.length > 4){
                value = '**** **** **** ' + value.slice(-4);
            } else {
                value = '**** **** **** 0000';
            }
            cardNumberDisplay.textContent = value;
        });

        cardNameInput.addEventListener('input', () => {
            cardNameDisplay.textContent = cardNameInput.value.toUpperCase() || 'AD SOYAD';
        });

        function updateExpire(){
            const month = expireMonthInput.value.padStart(2,'0');
            const year = expireYearInput.value.slice(-2);
            cardExpireDisplay.textContent = (month && year) ? `${month}/${year}` : 'MM/YY';
        }

        expireMonthInput.addEventListener('input', updateExpire);
        expireYearInput.addEventListener('input', updateExpire);

        // Başlangıçta kart önizlemesini doldur
        updateExpire();
    </script>
@endsection

@extends('superadmin.layouts.app')
@section('content')
    <style type="text/css">
        .tops { padding: 20px 10px; font-weight: bold; color: #fff; }
        .tops span { font-size: 15px; }
        .table thead tr { background: #ddd; }
        .table thead tr th {
            color: #000; font-size: 15px; height: 20px; overflow: hidden;
        }
        .bg-ok { background: #4f46e5; }
    </style>

    <div class="container-fluid">
        <!-- Başlık ve breadcrumb -->
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yönetici Kontör Hareketleri</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Kurye Hakediş</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Filtrele</a></li>
                </ol>
            </div>
        </div>

        <!-- Kontör Yükle ve İstatistikler -->
        <div class="row gap-4">
            <div class="col-md-6">
                @if(session()->has('message'))
                    <div class="custom-alert success">
                        <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span class="alert-message">{{ session()->get('message') }}</span>
                    </div>
                @endif
                @if(session()->has('test') )
                    <div class="custom-alert error">
                        <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span class="alert-message">{{ session()->get('test') }}</span>
                    </div>
                @endif
                <div class="row">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Kontör Yükle</h4></div>
                        <div class="card-body">
                            <div class="basic-form">
                                <form method="POST" action="{{ route('superadmin.admin_topup_create') }}">
                                    @csrf
                                    <input style="display: none" type="text" value="{{$admin->id}}" name="admin_id">
                                    <div class="mb-4 text-dark">
                                        <label>Kontör Ücreti Seçiniz</label>
                                        <select required name="top_up_price" id="top_up_price" class="form-control">
                                            <option value="0.50">0.50₺</option>
                                            <option value="1.5">1.5₺</option>
                                            <option value="2">2₺</option>
                                            <option value="2.5">2.5₺</option>
                                            <option value="3">3₺</option>
                                            <option value="4">4₺</option>
                                        </select>
                                    </div>
                                    <div class="mb-4 text-dark">
                                        <label>Kontör Adet</label>
                                        <input required class="form-control" type="number" placeholder="1 kontör = 1 paket" id="top_up" name="top_up">
                                    </div>
                                    <div class="mb-4 text-dark" id="total_container">
                                        <label>Toplam Tutar</label>
                                        <input class="form-control" type="text" id="total_amount" name="total_amount" readonly>
                                    </div>
                                    <button class="special-button" type="submit">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İstatistikler ve Filtreler -->
            <div class="col-md-5 card card-body">
                <div class="row">
                    <form method="GET" action="{{ route('superadmin.admin_topup', $admin->id) }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Bitiş Tarihi</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-secondary w-50">Filtrele</button>
                                <a href="{{ route('superadmin.admin_topup', $admin->id) }}" class="btn btn-danger w-50">Temizle</a>
                            </div>
                        </div>
                    </form>
                    <!-- İstatistik Kartlar -->
                    <div class="col-xl-12" id="reportList">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h4 class="card-title mb-4 text-center" id="selected-courier" style="font-weight: 700;"></h4>
                                <div class="row text-center">
                                    <!-- Mevcut Kontör -->
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded border-dark">
                                            <h6 class="mb-1 text-black">Mevcut Kontör</h6>
                                            <h4 class="text-black mb-0" id="order-count">{{$stats['current_balance']}}</h4>
                                        </div>
                                    </div>
                                    <!-- Toplam Alınan Kontör -->
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded bg-ok">
                                            <h6 class="mb-1 text-white">Toplam Alınan Kontör</h6>
                                            <h4 class="text-white mb-0" id="total-progress-payment">
                                                {{$stats['total_topup']}}
                                            </h4>
                                        </div>
                                    </div>
                                    <!-- Yapılan Ödeme -->
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded bg-ok">
                                            <h6 class="mb-1 text-white">Yapılan Ödeme</h6>
                                            <h4 class="text-white mb-0" id="paid-amount">
                                                {{$stats['paid_amount']}}₺
                                            </h4>
                                        </div>
                                    </div>
                                    <!-- Kalan Ödeme -->
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded bg-ok">
                                            <h6 class="mb-1 text-white">Kalan Ödeme</h6>
                                            <h4 class="text-white mb-0" id="remaining-amount">
                                                {{$stats['remaining_amount']}}₺
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yenile Link -->
        <a style="cursor: pointer" href="" class="text-danger fw-bold size-2" onclick="window.reload()"> Yenile</a>

        <!-- Tablar ve Veri Listesi -->
        <div class="row">
            <div class="row card">
                <div class="col-xl-12 card-body">
                    <!-- Sekmeler -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="talep-tab" data-bs-toggle="tab" data-bs-target="#talep" type="button" role="tab">Talep</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="eklenen-tab" data-bs-toggle="tab" data-bs-target="#eklenen" type="button" role="tab">Yüklenen Kontörler</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-danger fw-bold" id="ödemeBekleyen-tab" data-bs-toggle="tab" data-bs-target="#ödemeBekleyen" type="button" role="tab">Ödeme Bekliyor</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-success fw-bold" id="ödenen-tab" data-bs-toggle="tab" data-bs-target="#ödenen" type="button" role="tab">Ödenendi</button>
                        </li>
                    </ul>

                    <!-- Tab İçerikleri -->
                    <div class="tab-content" id="myTabContent">
                        <!-- Talep -->
                        <div class="tab-pane fade show active" id="talep" role="tabpanel" aria-labelledby="talep-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered shadow-hover card-table text-black" style="min-width: 845px" id="tablo-talepler">
                                    <thead>
                                    <tr>
                                        <th>Talep Eden</th>
                                        <th>Talep Eden Rolü</th>
                                        <th>Kontör Adet</th>
                                        <th>Kontör Ücreti</th>
                                        <th>Toplam Tutar</th>
                                        <th>Onayla</th>
                                        <th>Kayıt Tarihi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($records->where('is_approved', false) as $record)
                                        <tr id="data_{{ $record->id }}">
                                            <td>
                                                @switch($record->created_type)
                                                    @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name }} @break
                                                    @case('admin') {{ App\Models\Admin::find($record->created_by_user_id)->name }} @break
                                                    @case('dealer') {{ App\Models\User::find($record->created_by_user_id)->name }} @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($record->created_type)
                                                    @case('superadmin') Üst Yönetici @break
                                                    @case('admin') Yönetici @break
                                                    @case('dealer') Partner @break
                                                @endswitch
                                            </td>
                                            <td>{{ $record->top_up }} ₺</td>
                                            <td>{{ $record->top_up_price }} ₺</td>
                                            <td>{{ $record->total_amount }} ₺</td>
                                            <td>
                                                <div class="form-check form-switch me-3">
                                                    <label class="form-check-label text-dark fw-bold px-3 mt-3" for="s-{{$record->id}}"></label>
                                                    <input class="form-check-input" type="checkbox" id="s-{{$record->id}}"
                                                           role="switch" style="height: 30px; width: 60px;"
                                                           {{ $record->is_approved ? 'checked' : '' }}
                                                           onchange="approveFunction(this, '{{$record->id}}')">
                                                </div>
                                            </td>
                                            <td>{{ date('d-m-Y H:i:s',strtotime($record->created_at)) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div id="no-records-talep" style="display:none; text-align:center; margin-top:10px;">Veri bulunmuyor</div>
                            </div>
                        </div>

                        <!-- Yüklenenler -->
                        <div class="tab-pane fade" id="eklenen" role="tabpanel" aria-labelledby="eklenen-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-borderless shadow-hover card-table text-black" style="min-width: 845px" id="tablo-eklenen">
                                    <thead>
                                    <tr>
                                        <th>Ekleyen</th>
                                        <th>Talep Eden Rolü</th>
                                        <th>Kontör Adet</th>
                                        <th>Kontör Ücreti</th>
                                        <th>Toplam Tutar</th>
                                        <th>Kayıt Tarihi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($records->where('is_approved', true) as $record)
                                        <tr id="data_{{ $record->id }}">
                                            <td>
                                                @switch($record->created_type)
                                                    @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name }} @break
                                                    @case('admin') {{ App\Models\Admin::find($record->created_by_user_id)->name }} @break
                                                    @case('dealer') {{ App\Models\User::find($record->created_by_user_id)->name }} @break
                                                @endswitch
                                            </td>
                                            <td>{{ $record->created_type }}</td>
                                            <td>{{ $record->top_up }} ₺</td>
                                            <td>{{ $record->top_up_price }} ₺</td>
                                            <td>{{ $record->total_amount }} ₺</td>
                                            <td>{{ $record->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div id="no-records-eklenen" style="display:none; text-align:center; margin-top:10px;">Veri bulunmuyor</div>
                            </div>
                        </div>

                        <!-- Ödenenler -->
                        <div class="tab-pane fade" id="ödenen" role="tabpanel" aria-labelledby="ödenen-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-borderless shadow-hover card-table text-black" style="min-width: 845px" id="tablo-odenen">
                                    <thead>
                                    <tr>
                                        <th>Ekleyen</th>
                                        <th>Talep Eden Rolü</th>
                                        <th>Kontör Adet</th>
                                        <th>Kontör Ücreti</th>
                                        <th>Toplam Tutar</th>
                                        <th>Ödenmedi Yap</th>
                                        <th>Kayıt Tarihi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($records->where('is_approved', true)->where('is_paid',true) as $record)
                                        <tr id="data_{{ $record->id }}">
                                            <td>
                                                @switch($record->created_type)
                                                    @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name }} @break
                                                    @case('admin') {{ App\Models\Admin::find($record->created_by_user_id)->name }} @break
                                                    @case('dealer') {{ App\Models\User::find($record->created_by_user_id)->name }} @break
                                                @endswitch
                                            </td>
                                            <td>{{ $record->created_type }}</td>
                                            <td>{{ $record->top_up }} ₺</td>
                                            <td>{{ $record->top_up_price }} ₺</td>
                                            <td>{{ $record->total_amount }} ₺</td>
                                            <td>
                                                <div class="form-check form-switch me-3">
                                                    <label class="form-check-label text-dark fw-bold px-3 mt-3" for="s-{{$record->id}}"></label>
                                                    <input class="form-check-input" type="checkbox" id="s-{{$record->id}}"
                                                           role="switch" style="height: 30px; width: 60px;"
                                                           {{ $record->is_paid ? 'checked' : '' }}
                                                           onchange="unpaidFunction(this, '{{$record->id}}')">
                                                </div>
                                            </td>
                                            <td>{{ $record->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div id="no-records-odenen" style="display:none; text-align:center; margin-top:10px;">Veri bulunmuyor</div>
                            </div>
                        </div>

                        <!-- Ödeme Bekleyenler -->
                        <div class="tab-pane fade" id="ödemeBekleyen" role="tabpanel" aria-labelledby="ödemeBekleyen-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-borderless shadow-hover card-table text-black" style="min-width: 845px" id="tablo-odenmeyen">
                                    <thead>
                                    <tr>
                                        <th>Ekleyen</th>
                                        <th>Talep Eden Rolü</th>
                                        <th>Kontör Adet</th>
                                        <th>Kontör Ücreti</th>
                                        <th>Toplam Tutar</th>
                                        <th>Ödendi Yap</th>
                                        <th>Kayıt Tarihi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($records->where('is_approved', true)->where('is_paid',false) as $record)
                                        <tr id="data_{{ $record->id }}">
                                            <td>
                                                @switch($record->created_type)
                                                    @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name }} @break
                                                    @case('admin') {{ App\Models\Admin::find($record->created_by_user_id)->name }} @break
                                                    @case('dealer') {{ App\Models\User::find($record->created_by_user_id)->name }} @break
                                                @endswitch
                                            </td>
                                            <td>{{ $record->created_type }}</td>
                                            <td>{{ $record->top_up }} ₺</td>
                                            <td>{{ $record->top_up_price }} ₺</td>
                                            <td>{{ $record->total_amount }} ₺</td>
                                            <td>
                                                <div class="form-check form-switch me-3">
                                                    <label class="form-check-label text-dark fw-bold px-3 mt-3" for="s-{{$record->id}}"></label>
                                                    <input class="form-check-input" type="checkbox" id="s-{{$record->id}}"
                                                           role="switch" style="height: 30px; width: 60px;"
                                                           {{ $record->is_paid ? 'checked' : '' }}
                                                           onchange="paidFunction(this, '{{$record->id}}')">
                                                </div>
                                            </td>
                                            <td>{{ $record->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div id="no-records-odemeyen" style="display:none; text-align:center; margin-top:10px;">Veri bulunmuyor</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Her tablo ve kart için "Veri Yok" mesajını kontrol ve göster
        function checkAndShowNoData(tableId, noDataId) {
            const table = document.getElementById(tableId);
            const noDataDiv = document.getElementById(noDataId);
            if (table && table.querySelector("tbody").rows.length === 0) {
                noDataDiv.style.display = "block";
            } else {
                noDataDiv.style.display = "none";
            }
        }

        // Sayfa yüklendiğinde kontrol
        document.addEventListener("DOMContentLoaded", () => {
            checkAndShowNoData("tablo-talepler", "no-records-talep");
            checkAndShowNoData("tablo-eklenen", "no-records-eklenen");
            checkAndShowNoData("tablo-odenen", "no-records-odenen");
            checkAndShowNoData("tablo-odenmeyen", "no-records-odemeyen");
        });

        // Filtreleme fonksiyonu
        function filterTable(searchInputId, tableId, noDataId) {
            const input = document.getElementById(searchInputId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const tbody = table.querySelector("tbody");
            let visibleCount = 0;

            Array.from(tbody.rows).forEach(row => {
                const rowText = row.innerText.toLowerCase();
                if (rowText.includes(filter)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            // "Veri bulunmuyor" gösterimi
            document.getElementById(noDataId).style.display = visibleCount === 0 ? "block" : "none";
        }

        // Filtreleme olayları
        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('keyup', () => {
                const targetId = input.dataset.target;
                const noDataId = input.dataset.nodata;
                filterTable(input.id, targetId, noDataId);
            });
        });
    </script>

    <!-- Toplam tutar hesaplama -->
    <script>
        const priceSelect = document.getElementById("top_up_price");
        const topUpInput = document.getElementById("top_up");
        const totalInput = document.getElementById("total_amount");

        function updateTotal() {
            const price = parseFloat(priceSelect.value) || 0;
            const qty = parseInt(topUpInput.value) || 0;
            const total = (price * qty).toFixed(2);
            totalInput.value = total + "₺";
        }

        priceSelect.addEventListener("change", updateTotal);
        topUpInput.addEventListener("input", updateTotal);
    </script>

    <!-- Onay ve ödeme işlemleri -->
    <script>
        function approveFunction(checkbox, id) {
            const currentState = checkbox.checked;
            Swal.fire({
                title: 'Onayla',
                text: "Onaylamak istediğinizden emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#4f46e5',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Onaylamak istiyorum!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/superadmin/admin/topup/approve/' + id,
                        success: (data) => {
                            if (data === "OK") {
                                $('#data_' + id).fadeOut(300, () => { $(this).remove(); });
                                Swal.fire("Onaylamak!", "Güncelleme işlemi başarılı.", "success");
                                checkAndShowNoData("tablo-talepler", "no-records-talep");
                            } else {
                                Swal.fire("Uyarı!", "Onaylanamadı.", "warning");
                                checkbox.checked = !currentState;
                            }
                        },
                        error: () => {
                            Swal.fire("Hata!", "Bir hata oluştu, lütfen tekrar deneyin.", "error");
                            checkbox.checked = !currentState;
                        }
                    });
                } else {
                    checkbox.checked = !currentState;
                }
            });
        }

        function paidFunction(checkbox, id) {
            const currentState = checkbox.checked;
            Swal.fire({
                title: 'Ödendi Yap',
                text: "Ödendi yapmak istediğinizden emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#4f46e5',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Onaylamak istiyorum!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/superadmin/admin/topup/paid/' + id,
                        success: (data) => {
                            if (data === "OK") {
                                $('#data_' + id).fadeOut(300, () => { $(this).remove(); });
                                Swal.fire("Onaylamak!", "Ödeme işlemi başarılı.", "success");
                                checkAndShowNoData("tablo-odenen", "no-records-odenen");
                            } else {
                                Swal.fire("Uyarı!", "Onaylanamadı.", "warning");
                                checkbox.checked = !currentState;
                            }
                        },
                        error: () => {
                            Swal.fire("Hata!", "Bir hata oluştu, lütfen tekrar deneyin.", "error");
                            checkbox.checked = !currentState;
                        }
                    });
                } else {
                    checkbox.checked = !currentState;
                }
            });
        }

        function unpaidFunction(checkbox, id) {
            const currentState = checkbox.checked;
            Swal.fire({
                title: 'Ödenmedi Yap',
                text: "Ödenmedi yapmak istediğinizden emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#4f46e5',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Onaylamak istiyorum!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/superadmin/admin/topup/unpaid/' + id,
                        success: (data) => {
                            if (data === "OK") {
                                $('#data_' + id).fadeOut(300, () => { $(this).remove(); });
                                Swal.fire("Onaylamak!", "Ödenmedi işlemi başarılı.", "success");
                                checkAndShowNoData("tablo-odenmeyen", "no-records-odemeyen");
                            } else {
                                Swal.fire("Uyarı!", "Onaylanamadı.", "warning");
                                checkbox.checked = !currentState;
                            }
                        },
                        error: () => {
                            Swal.fire("Hata!", "Bir hata oluştu, lütfen tekrar deneyin.", "error");
                            checkbox.checked = !currentState;
                        }
                    });
                } else {
                    checkbox.checked = !currentState;
                }
            });
        }
    </script>

    <!-- Filtreleme Inputları ve olaylar -->
    <script type="text/javascript">
        // Filtreleme inputları ve olaylar
        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('keyup', () => {
                const targetId = input.dataset.target;
                const noDataId = input.dataset.nodata;
                filterTable(input.id, targetId, noDataId);
            });
        });
    </script>

    <!-- Toplam tutar hesaplama -->
    <script>
        const priceSelect = document.getElementById("top_up_price");
        const topUpInput = document.getElementById("top_up");
        const totalInput = document.getElementById("total_amount");

        function updateTotal() {
            const price = parseFloat(priceSelect.value) || 0;
            const qty = parseInt(topUpInput.value) || 0;
            const total = (price * qty).toFixed(2);
            totalInput.value = total + "₺";
        }

        priceSelect.addEventListener("change", updateTotal);
        topUpInput.addEventListener("input", updateTotal);
    </script>

    <!-- Son olarak, sayfa yüklendiğinde tüm tablolara ve kartlara veri kontrolü -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            checkAndShowNoData("tablo-talepler", "no-records-talep");
            checkAndShowNoData("tablo-eklenen", "no-records-eklenen");
            checkAndShowNoData("tablo-odenen", "no-records-odenen");
            checkAndShowNoData("tablo-odenmeyen", "no-records-odemeyen");
        });
    </script>
@endsection

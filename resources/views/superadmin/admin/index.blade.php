@extends('superadmin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yöneticiler</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Yöneticiler</a></li>
                <li class="breadcrumb-item active">Liste</li>
            </ol>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter" placeholder="Partner ara...">
                    <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{ route('superadmin.admin_create') }}" class="special-button me-3 mb-2">
                    <i class="fas fa-user me-2"></i>+ Yeni Ekle
                </a>
                <a href="javascript:void(0);" onclick="location.reload();" class="special-ok-button  mb-2">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table id="courierTable" class="t table-hover text-black">
                    <thead>
                    <tr>
                        <th>İsim Soyisim</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Kod</th>
                        <th>Şehir</th>
                        <th>İlçe</th>
                        <th>Kontör Ücreti</th>
                        <th>Eklenme Tarihi</th>
                        <th>Durum <small>(Aktif/Pasif)</small></th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $admin)
                        <tr id="data_{{ $admin->id }}">
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->phone }}</td>
                            <td>{{ $admin->code }}</td>
                            <td>{{ $admin->city_id ? \App\Models\City::find($admin->city_id)->name : '-' }}</td>
                            <td>{{ $admin->district_id ? \App\Models\District::find($admin->district_id)->name : '-'}}</td>
                            <td>{{ number_format($admin->top_up_price,2) }}</td>
                            <td>{{ $admin->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <div class="form-check form-switch me-3">
                                    <label class="form-check-label text-dark fw-bold px-3 mt-3" for="s-{{$admin->id}}"></label>
                                    <input class="form-check-input" type="checkbox" id="s-{{$admin->id}}"
                                           role="switch" style="height: 30px; width: 60px;"
                                           {{ $admin->is_active ? 'checked' : '' }}
                                           onchange="event.preventDefault(); StatusFunction(this, '{{$admin->id}}')">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('superadmin.admin_topup', $admin->id) }}" class="btn btn-secondary btn-xs me-1">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('superadmin.admin_edit', $admin->id) }}" class="btn btn-primary btn-xs me-1">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button onclick="DeleteFunction({{ $admin->id }})" class="btn btn-danger btn-xs">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#courierTable').DataTable({
                order: [[3, "desc"]], // created_at sütunu
                language: {
                    search: "Ara:",
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json",
                    lengthMenu: "Sayfa başına _MENU_ kayıt",
                    info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
                    infoEmpty: "Gösterilecek kayıt yok",
                    paginate: {
                        next: "Sonraki",
                        previous: "Önceki"
                    }
                }
            });

            $('#custom-filter').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function StatusFunction(checkbox, id) {
            const currentState = checkbox.checked;

            Swal.fire({
                title: 'Durum Güncelleme',
                text: "Güncellemek istediğinizden emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#4f46e5',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Güncellemek istiyorum!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/superadmin/admin/status/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire("Güncellendi!", "Güncelleme işlemi başarılı.", "success");
                                // Başarılıysa checkbox olduğu gibi bırak
                            } else {
                                Swal.fire("Uyarı!", "Bu Partner Güncellenmiyor.", "warning");
                                checkbox.checked = !currentState; // Eski haline döndür
                            }
                        },
                        error: function () {
                            Swal.fire("Hata!", "Bir hata oluştu, lütfen tekrar deneyin.", "error");
                            checkbox.checked = !currentState; // Eski haline döndür
                        }
                    });
                } else {
                    checkbox.checked = !currentState; // Kullanıcı onaylamadıysa eski haline döndür
                }
            });
        }

        function DeleteFunction(id) {
            Swal.fire({
                title: 'Silmek istediğinizden emin misiniz?',
                text: "Bu işlemi geri alamazsınız!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#4f46e5',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Silmek istiyorum!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/superadmin/admin/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                $('#data_' + id).fadeOut(300, function () {
                                    $(this).remove();
                                });
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                            } else {
                                Swal.fire("Uyarı!", "Bu kuryeyi silemezsiniz.", "warning");
                            }
                        },
                        error: function () {
                            Swal.fire("Hata!", "Bir hata oluştu, lütfen tekrar deneyin.", "error");
                        }
                    });
                }
            });
        }
    </script>

    <style>

    </style>
@endsection

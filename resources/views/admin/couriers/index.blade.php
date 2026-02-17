@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kuryeler</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Kuryeler</a></li>
                <li class="breadcrumb-item active">Liste</li>
            </ol>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter" placeholder="Kurye ara...">
                    <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{ route('admin.couriers.new') }}" class="special-button me-3 mb-2">
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
                        <th>Kurye Adı</th>
                        <th>Telefon Numarası</th>
                        <th>Çalışma Şekli</th>
                        <th>Paket Başı Ücret</th>
                        <th>Sabit Ücret</th>
                        <th>1 Km Ücreti</th>
                        <th>Oluşturulma</th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($couriers as $courier)
                        <tr id="data_{{ $courier->id }}">
                            <td>{{ $courier->name }}</td>
                            <td>{{ $courier->phone }}</td>
                            <td class="text-danger">{{ $courier->price_type == 'package' ? 'Paket Başı' : 'Sabit + Km' }}</td>
                            <td>{{ number_format($courier->price, 2) }} ₺</td>
                            <td>{{ number_format($courier->fixed_price, 2) }} ₺</td>
                            <td>{{ number_format($courier->km_price, 2) }} ₺</td>
                            <td>{{ $courier->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('admin.couriers.report', $courier->id) }}" class="btn btn-secondary btn-xs me-1">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.couriers.edit', $courier->id) }}" class="special-button btn-xs me-1">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button onclick="DeleteFunction({{ $courier->id }})" class="btn btn-danger btn-xs">
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
                        url: '/admin/couriers/delete/' + id,
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
@endsection

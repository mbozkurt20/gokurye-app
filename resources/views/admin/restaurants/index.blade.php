@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurantlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Restaurantlar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter-restaurants" placeholder="Restaurant ara..">
                    <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{ route('admin.restaurants.new') }}" class="special-button me-3">
                    <i class="fas fa-shopping me-2"></i>+ Yeni Ekle
                </a>
                <a href="javascript:void(0);" onclick="location.reload();" class="special-ok-button  mb-2">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="table-responsive card card-body">
                    <table id="example315" class="display" style="min-width: 845px">
                        <thead>
                        <tr>
                            <th style="width: 10%">İşyeri Kodu <i class="fa fa-filter text-danger"></i></th>
                            <th style="width: 20%">İşyeri Adı</th>
                            <th style="width: 10%">Yetkili Adı</th>
                            <th style="width: 10%">E-posta</th>
                            <th style="width: 10%">Telefon</th>
                            <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($restaurants as $restaurant)
                            <tr id="data_{{ $restaurant->id }}">
                                <td>#{{ $restaurant->restaurant_code }}</td>
                                <td class="wspace-no">{{ $restaurant->restaurant_name }}</td>
                                <td>{{ $restaurant->name }}</td>
                                <td class="text-ov">{{ $restaurant->email }}</td>
                                <td class="text-ov">{{ $restaurant->phone }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('admin.restaurants.edit', ['id' => $restaurant->id]) }}" class="special-button shadow btn-xs sharp me-1">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a onclick="DeleteFunction({{ $restaurant->id }})" class="btn btn-danger shadow btn-xs sharp">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    @if(!count($restaurants))
                        <h4 class="text-center mt-4">Restaurant Bulunmamaktadır.</h4>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#example315').DataTable({
                order: [[0, 'asc']], // 0. sütun (İşyeri Kodu) artan sırada başlat
                language: {
                    "sEmptyTable": "Tabloda herhangi bir veri mevcut değil",
                    "sInfo": "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
                    "sInfoEmpty": "Kayıt yok",
                    "sInfoFiltered": "(_MAX_ kayıt içerisinden filtrelendi)",
                    "sLengthMenu": "Sayfada _MENU_ kayıt göster",
                    "sLoadingRecords": "Yükleniyor...",
                    "sProcessing": "İşleniyor...",
                    "sSearch": "Ara:",
                    "sZeroRecords": "Eşleşen kayıt bulunamadı",
                    "oPaginate": {
                        "sFirst": "İlk",
                        "sLast": "Son",
                        "sNext": "Sonraki",
                        "sPrevious": "Önceki"
                    },
                    "oAria": {
                        "sSortAscending": ": artan sütun sıralamasını aktifleştir",
                        "sSortDescending": ": azalan sütun sıralamasını aktifleştir"
                    }
                }
            });

            // Özel arama kutusunu DataTable ile entegre et
            $('#custom-filter-restaurants').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function DeleteFunction(id) {
            Swal.fire({
                title: 'Silmek istediğinizden emin misiniz ?',
                text: "Bu işlemi geri alamazsınız!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#4f46e5',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Silmek istiyorum!'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/admin/restaurants/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + id).fadeOut(function () {
                                    $(this).remove();
                                });
                            } else if (data === "NO") {
                                Swal.fire("Uyarı !!", "Bu restaurantı silemezsiniz.", "warning");
                            }
                        },
                        error: function () {
                            Swal.fire("Hata!", "Silme sırasında bir sorun oluştu.", "error");
                        }
                    });
                }
            });
        }
    </script>
@endsection

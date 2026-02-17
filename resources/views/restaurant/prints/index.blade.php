@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yazıcılar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yazıcılar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <input type="text" id="print-filter-category" class="form-control" placeholder="Yazıcı ara..">
            </div>
            <a href="{{ route('restaurant.prints.new') }}" class="special-button"><i class="fas fa-plus me-2"></i>Yeni Ekle</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="prints-table" class="table table-bordered text-black" style="min-width: 600px;">
                        <thead>
                        <tr>
                            <th>Yazıcı Adı</th>
                            <th>Yazıcı Açıklaması</th>
                            <th>Oluşturulma Tarihi</th>
                            <th style="width: 10%;">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($prints as $print)
                            <tr id="data_{{$print->id}}">
                                <td>{{ $print->name }}</td>
                                <td>{{ $print->description }}</td>
                                <td>{{ $print->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('restaurant.prints.edit', ['id' => $print->id]) }}" class="special-button btn-sm me-1">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button onclick="DeleteFunction({{ $print->id }})" class="btn btn-danger btn-sm">
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
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#prints-table').DataTable({
                order: [[1, 'desc']], // created_at sütununa göre sıralama, desc
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

            $('#print-filter-category').on('keyup', function() {
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
                confirmButtonText: 'Evet, Silmek istiyorum!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/prints/delete/' + id,
                        success: function(data) {
                            if (data === "OK") {
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + id).fadeOut(function() { $(this).remove(); });
                            } else if (data === "NO") {
                                Swal.fire("Uyarı!", "Bu yazıcıyı silemezsiniz.", "warning");
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            Swal.fire("Hata!", "Silme işlemi başarısız oldu.", "error");
                        }
                    });
                }
            });
        }
    </script>
@endsection

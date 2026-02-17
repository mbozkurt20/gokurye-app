@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Giderler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Giderler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter-expenses" placeholder="Giderler ara..">
                    <span class="input-group-text">
                    <a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a>
                </span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{ route('admin.expenses.new') }}" class="special-button me-3">
                    <i class="fas fa-plus me-2"></i> Gider Ekle
                </a>
                <a href="javascript:void(0);" onclick="location.reload();" class="special-ok-button  mb-2">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>

        <div class="row card">
            <div class="col-xl-12 card-body">
                <div class="table-responsive">
                    <table id="example315" class="order-table shadow-hover card-table text-black" style="min-width: 845px">
                        <thead>
                        <tr>
                            <th>Gider Başlığı <i class="fa fa-filter text-danger"></i></th>
                            <th>Gider Tarihi <i class="fa fa-filter text-danger"></i></th>
                            <th>Gider Türü <i class="fa fa-filter text-danger"></i></th>
                            <th>Ödeme Method <i class="fa fa-filter text-danger"></i></th>
                            <th>İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($expenses as $expense)
                            <tr id="data_{{ $expense->id }}">
                                <td>{{ $expense->title }}</td>
                                <td>{{ $expense->date }}</td>
                                <td>{{ $expense->expense_type }}</td>
                                <td>{{ $expense->payment_method }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('admin.expenses.edit', ['id' => $expense->id]) }}" class="special-button shadow btn-xs sharp me-1">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a onclick="DeleteFunction({{ $expense->id }})" class="btn btn-danger shadow btn-xs sharp">
                                            <i class="fa fa-trash"></i>
                                        </a>
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

    <!-- Search & Delete Scripts -->
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#example315').DataTable({
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

            $('#custom-filter-expenses').on('keyup', function () {
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
                        url: '/admin/expenses/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + id).fadeOut(() => $(this).remove());
                            } else if (data === "NO") {
                                Swal.fire("Uyarı!", "Bu gideri silemezsiniz.", "warning");
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

@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Hakediş Ödemeleri</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Hakediş</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter-payments" placeholder="Hakediş ara..">
                    <span class="input-group-text">
                    <a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a>
                </span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{ route('admin.progress_payment.records.new') }}" class="special-button me-3">
                    <i class="fas fa-plus me-2"></i> Hakediş Ekle
                </a>
                <a href="javascript:void(0);" onclick="location.reload();" class="special-ok-button  mb-2">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>


    </div>

    <!-- Search & Delete Scripts -->
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#paymentsTable').DataTable();

            $('#custom-filter-payments').on('keyup', function () {
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
                        url: '/admin/progress_payment/records/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + id).fadeOut(() => $(this).remove());
                            } else if (data === "NO") {
                                Swal.fire("Uyarı!", "Bu kaydı silemezsiniz.", "warning");
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

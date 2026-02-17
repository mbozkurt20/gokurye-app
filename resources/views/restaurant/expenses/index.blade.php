@extends('restaurant.layouts.app')
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
                    <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                </div>
            </div>
            <a href="{{route('expenses.new')}}" class="special-button me-3"><i class="fas fa-plus me-2"></i> Yeni Ekle</a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="exampleExpenses" class="table table-bordered text-black" style="min-width: 845px">
                        <thead>
                        <tr>
                            <th>Gider Başlığı</th>
                            <th>Gider Tarihi</th>
                            <th>Gider Türü</th>
                            <th>Ödeme Method</th>
                            <th>İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($expenses as $expense)
                            <tr id="data_{{$expense->id}}-expense">
                                <td>{{$expense->title}}</td>
                                <td>{{$expense->date}}</td>
                                <td>{{$expense->expense_type}}</td>
                                <td>{{$expense->payment_method}}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{route('expenses.edit', ['id' => $expense->id])}}" class="special-button btn-sm me-1 shadow sharp"><i class="fas fa-pencil-alt"></i></a>
                                        <button onclick="DeleteFunction({{$expense->id}})" class="btn btn-danger btn-sm shadow sharp"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    @if($expenses->isEmpty())
                        <div class="text-center mt-4">
                            <h4>Gider Bulunmuyor...</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            var table = $('#exampleExpenses').DataTable({
                order: [[1, 'desc']], // Tarihe göre desc sıralama
                language: {
                    search: "Ara:",
                    lengthMenu: "Sayfa başına _MENU_ kayıt",
                    info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
                    infoEmpty: "Gösterilecek kayıt yok",
                    paginate: {
                        next: "Sonraki",
                        previous: "Önceki"
                    }
                }
            });

            $('#custom-filter-expenses').keyup(function() {
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
            }).then(function(result) {
                if(result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/expenses/delete/' + id,
                        success: function(data) {
                            if(data == "OK") {
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + id + "-expense").fadeOut(function() { $(this).remove(); });
                            } else if(data == "NO") {
                                Swal.fire("Uyarı!", "Bu Gideri silemezsiniz.", "warning");
                            }
                        },
                        error: function() {
                            Swal.fire("Hata!", "Silme işlemi başarısız oldu.", "error");
                        }
                    });
                }
            });
        }
    </script>
@endsection

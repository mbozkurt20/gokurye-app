@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Stok Takibi</h2>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Ürün Stok Yönetimi</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-3 mb-5 text-sm font-semibold text-emerald-700">
        <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('restaurant.stock.update') }}">
        @csrf
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <p class="text-xs font-black text-slate-500 uppercase tracking-widest">Ürün Listesi ({{ $products->count() }} ürün)</p>
                <button type="submit" class="bg-brand text-white text-xs font-black uppercase tracking-wide px-5 py-2.5 rounded-2xl">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Kaydet
                </button>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <th class="px-6 py-3 text-left">Ürün Adı</th>
                        <th class="px-4 py-3 text-center">Stok Takibi</th>
                        <th class="px-4 py-3 text-center">Stok Miktarı</th>
                        <th class="px-4 py-3 text-center">Durum</th>
                        <th class="px-4 py-3 text-center">Hızlı Ayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/50 transition-colors" id="row-{{ $product->id }}">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800">{{ $product->name }}</p>
                            <p class="text-[11px] text-slate-400 font-semibold">₺{{ number_format($product->price, 2, ',', '.') }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="stock[{{ $product->id }}][enabled]" value="1"
                                       class="sr-only peer stock-toggle" data-id="{{ $product->id }}"
                                       {{ $product->stock_enabled ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-slate-200 peer-focus:ring-2 peer-focus:ring-brand/20 rounded-full peer
                                            peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                            after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                            </label>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <input type="number" name="stock[{{ $product->id }}][qty]"
                                   value="{{ $product->stock ?? 0 }}" min="0"
                                   class="w-20 text-center border border-slate-200 rounded-xl px-2 py-1.5 text-sm font-bold text-slate-800 focus:outline-none focus:border-brand qty-input"
                                   data-id="{{ $product->id }}"
                                   {{ $product->stock_enabled ? '' : 'disabled' }}>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if(!$product->stock_enabled)
                                <span class="text-[10px] font-black px-3 py-1 rounded-full bg-slate-100 text-slate-400 uppercase tracking-wide">Takip Yok</span>
                            @elseif($product->stock <= 0)
                                <span class="text-[10px] font-black px-3 py-1 rounded-full bg-rose-100 text-rose-500 uppercase tracking-wide">Tükendi</span>
                            @elseif($product->stock <= 5)
                                <span class="text-[10px] font-black px-3 py-1 rounded-full bg-amber-100 text-amber-500 uppercase tracking-wide">Az Kaldı</span>
                            @else
                                <span class="text-[10px] font-black px-3 py-1 rounded-full bg-emerald-100 text-emerald-600 uppercase tracking-wide">Stokta</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="adjustStock({{ $product->id }}, 'subtract', 1)"
                                        class="w-7 h-7 rounded-full bg-rose-100 text-rose-500 font-black text-sm flex items-center justify-center hover:bg-rose-200 transition-colors">−</button>
                                <button type="button" onclick="adjustStock({{ $product->id }}, 'add', 1)"
                                        class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 font-black text-sm flex items-center justify-center hover:bg-emerald-200 transition-colors">+</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">Aktif ürün bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-brand text-white text-sm font-black uppercase tracking-wide px-8 py-3 rounded-2xl shadow-lg shadow-brand/20">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Tüm Değişiklikleri Kaydet
            </button>
        </div>
    </form>

</div>

<script>
document.querySelectorAll('.stock-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        var id = this.dataset.id;
        var input = document.querySelector('.qty-input[data-id="' + id + '"]');
        if (input) input.disabled = !this.checked;
    });
});

function adjustStock(id, action, qty) {
    fetch('{{ url("/restaurant/stock/adjust") }}/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ action: action, qty: qty })
    }).then(r => r.json()).then(data => {
        var input = document.querySelector('.qty-input[data-id="' + id + '"]');
        if (input) input.value = data.stock;
    });
}
</script>
@endsection

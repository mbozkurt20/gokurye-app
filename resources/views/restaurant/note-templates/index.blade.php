@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    <div class="mb-6">
        <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Sipariş Notu Şablonları</h2>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Hızlı Not Şablonları</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-3 mb-5 text-sm font-semibold text-emerald-700">
        <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Yeni Şablon Formu --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                    <i class="fa-solid fa-plus mr-1 text-brand"></i> Yeni Şablon
                </p>
                <form method="POST" action="{{ route('restaurant.note-templates.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Başlık</label>
                            <input type="text" name="title" maxlength="100" required
                                   placeholder="Örn: Soğuk teslim, Acele sipariş..."
                                   class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:border-brand placeholder:text-slate-300">
                            @error('title')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Şablon Metni</label>
                            <textarea name="body" rows="5" maxlength="500" required
                                      placeholder="Sipariş notunda görünecek metin..."
                                      class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:border-brand placeholder:text-slate-300 resize-none"></textarea>
                            @error('body')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit"
                                class="w-full bg-brand text-white text-sm font-black uppercase tracking-wide py-3 rounded-2xl shadow-lg shadow-brand/20 hover:opacity-90 transition-opacity">
                            <i class="fa-solid fa-plus mr-2"></i> Şablon Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Şablon Listesi --}}
        <div class="lg:col-span-2">
            <div class="space-y-3">
                @forelse($templates as $template)
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 group">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fa-solid fa-note-sticky text-brand text-sm"></i>
                                <p class="text-sm font-black text-slate-800">{{ $template->title }}</p>
                            </div>
                            <p class="text-xs font-semibold text-slate-500 leading-relaxed bg-slate-50 rounded-2xl px-4 py-3">
                                {{ $template->body }}
                            </p>
                            <p class="text-[10px] text-slate-300 font-semibold mt-2">
                                {{ \Carbon\Carbon::parse($template->created_at)->format('d.m.Y H:i') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="copyTemplate('{{ addslashes($template->body) }}')"
                                    class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center hover:bg-indigo-200 transition-colors"
                                    title="Kopyala">
                                <i class="fa-solid fa-copy text-xs"></i>
                            </button>
                            <form method="POST" action="{{ route('restaurant.note-templates.destroy', $template->id) }}" onsubmit="return confirm('Şablonu sil?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center hover:bg-rose-200 transition-colors"
                                        title="Sil">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-12 text-center">
                    <i class="fa-solid fa-note-sticky text-4xl text-slate-200 mb-3"></i>
                    <p class="text-sm font-bold text-slate-400">Henüz şablon eklenmedi.</p>
                    <p class="text-xs text-slate-300 font-semibold mt-1">Sol taraftan ilk şablonunuzu ekleyin.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script>
function copyTemplate(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Şablon metni kopyalandı!');
    });
}
</script>
@endsection

@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid py-6">
        <div class="mb-8 flex flex-wrap items-center justify-between">
            <div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900 mb-1">Uygulamalar</h2>
                <nav class="flex text-sm font-medium text-slate-400">
                    <a href="javascript:void(0)" class="hover:text-indigo-600">Uygulamalar</a>
                    <span class="mx-2">/</span>
                    <span class="text-indigo-600">Yükle</span>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5 col-lg-4">
                <div class="group relative rounded-[2.5rem] border border-slate-100 bg-white p-2 shadow-xl shadow-slate-200/50 transition-all duration-500 hover:-translate-y-2 hover:shadow-indigo-500/10">
                    <div class="relative overflow-hidden rounded-[2rem] bg-slate-50 p-8">
                        <img src="/theme/images/print.png"
                             class="mx-auto h-48 w-auto object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-110"
                             alt="{{config('site.name')}} Yazıcı">
                    </div>

                    <div class="p-8 text-center">
                        <h5 class="mb-3 text-2xl font-black tracking-tight text-slate-900">{{config('site.name')}} Yazıcı</h5>
                        <p class="mb-8 text-sm leading-relaxed text-slate-500">
                            Yazıcınızı kurarken sizden istenen <span class="font-bold text-slate-700 text-uppercase">Restaurant ID</span> bilgisini aşağıda bulabilirsiniz.
                        </p>

                        <div class="relative mb-10 inline-flex items-center justify-center">
                            <div class="absolute inset-0 animate-ping rounded-full bg-indigo-500/20 opacity-75"></div>
                            <div class="relative flex h-24 w-24 items-center justify-center rounded-full bg-slate-900 text-3xl font-black text-white shadow-2xl shadow-indigo-500/40 ring-8 ring-slate-50">
                                {{ auth()->id() }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">
                            <a href="/apps/go-kurye.exe" download
                               class="flex items-center justify-center gap-3 rounded-2xl bg-indigo-600 px-8 py-4 text-sm font-extrabold text-white transition-all hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30">
                                <i class="fa-solid fa-download text-lg"></i>
                                ŞİMDİ İNDİR (v1.0.4)
                            </a>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Windows 10/11 Uyumlu</p>
                        </div>
                    </div>

                    <div class="absolute -bottom-2 -right-2 -z-10 h-full w-full rounded-[2.5rem] bg-indigo-50 transition-all group-hover:bottom-0 group-hover:right-0"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

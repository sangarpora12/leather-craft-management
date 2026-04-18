@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-zinc-900 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-zinc-900/50 border-b border-zinc-800 p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white">Laporan Operasional</h1>
                    <p class="text-gray-400 text-sm mt-1">Ringkasan aktivitas produksi dan gudang.</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M10.5 4.5V1.81055C10.5 1.61156 10.421 1.28016L9.21961 0.219609C9.07898 0.0585938 8.8882 0 8.68922 0H2.25C1.83586 0 1.5 0.335859 1.5 0.75V4.5C0.671484 4.5 0 5.17148 0 6V8.625C0 8.83219 0.167812 9 0.375 9H1.5V11.25C1.5 11.6641 1.83586 12 2.25 12H9.75C10.1641 12 10.5 11.6641 10.5 11.25V9H11.625C11.8322 9 12 8.83219 12 8.625V6C12 5.17148 11.3285 4.5 10.5 4.5ZM9 10.5H3V8.25H9V10.5ZM9 5.25H3V1.5H7.5V2.625C7.5 2.83219 7.66781 3 7.875 3H9V5.25ZM10.125 6.9375C9.81445 6.9375 9.5625 6.68555 9.5625 6.375C9.5625 6.06422 9.81445 5.8125 10.125 5.8125C10.4355 5.8125 10.6875 6.06422 10.6875 6.375C10.6875 6.68555 10.4355 6.9375 10.125 6.9375Z" fill="currentColor"/>
                        </svg>
                        Cetak Laporan
                    </button>
                    <a href="{{ route('admin.laporan.lengkap') }}" class="flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M11.4369 0H0.563154C0.0635131 0 -0.188604 0.606234 0.165419 0.960258L4.5 5.29549V10.125C4.5 10.3085 4.58955 10.4805 4.73993 10.5858L6.61493 11.8979C6.98484 12.1568 7.5 11.8944 7.5 11.437V5.29549L11.8347 0.960258C12.188 0.606938 11.9376 0 11.4369 0Z" fill="currentColor"/>
                        </svg>
                        Laporan Lengkap
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-zinc-900 p-6 border-b border-zinc-800">
            <div class="flex items-center gap-2 mb-4">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M15.5 12H5V11.5C5 11.225 4.775 11 4.5 11H3.5C3.225 11 3 11.225 3 11.5V12H0.5C0.225 12 0 12.225 0 12.5V13.5C0 13.775 0.225 14 0.5 14H3V14.5C3 14.775 3.225 15 3.5 15H4.5C4.775 15 5 14.775 5 14.5V14H15.5C15.775 14 16 13.775 16 13.5V12.5C16 12.225 15.775 12 15.5 12Z" fill="#F97316"/>
                </svg>
                <h3 class="text-white text-xl font-bold">Filter Laporan</h3>
            </div>
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1">
                    <label class="block text-gray-500 text-xl font-semibold mb-4">Dari Tanggal & Jam</label>
                    <input type="datetime-local" name="from" value="{{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d\TH:i') : \Carbon\Carbon::now()->startOfMonth()->startOfDay()->format('Y-m-d\T00:00') }}" class="w-full bg-neutral-900 border-2 border-zinc-700 rounded-xl px-6 py-6 text-white text-xl font-medium h-20">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-500 text-xl font-semibold mb-4">Sampai Tanggal & Jam</label>
                    <input type="datetime-local" name="to" value="{{ $to ? \Carbon\Carbon::parse($to)->format('Y-m-d\TH:i') : \Carbon\Carbon::now()->endOfMonth()->endOfDay()->format('Y-m-d\T23:59') }}" class="w-full bg-neutral-900 border-2 border-zinc-700 rounded-xl px-6 py-6 text-white text-xl font-medium h-20">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="flex items-center gap-3 bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-xl text-lg font-bold transition-colors h-20">
                        <svg width="16" height="16" viewBox="0 0 12 12" fill="none">

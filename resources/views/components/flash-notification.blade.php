{{-- NOTIFIKASI SUKSES / ERROR UMUM --}}
@if(session()->has('success') || session()->has('error') || session()->has('info'))
<div id="flash-notification" class="fixed top-6 right-6 z-[100] max-w-sm w-full px-4 md:px-0 transition-all duration-500 transform translate-y-0 opacity-100">
    @php
        $type = session('success') ? 'success' : (session('error') ? 'error' : 'info');
        $message = session($type);
        $bgColor = $type == 'success' ? 'bg-green-600 border-green-500' : ($type == 'error' ? 'bg-red-600 border-red-500' : 'bg-blue-600 border-blue-500');
    @endphp
    
    <div class="{{ $bgColor }} text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 border border-white/10 backdrop-blur-md">
        <div class="flex-shrink-0">
            @if($type == 'success')
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium leading-tight">{{ $message }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-white/70 hover:text-white ml-2">✕</button>
    </div>
</div>
@endif

{{-- NOTIFIKASI ERROR VALIDASI (FORMULIR) --}}
@if($errors->any())
<div id="validation-notification" class="fixed top-20 right-6 z-[100] max-w-sm w-full px-4 md:px-0 transition-all duration-500">
    <div class="bg-red-600 border border-red-500 text-white px-4 py-3 rounded-xl shadow-2xl backdrop-blur-md">
        <div class="flex items-center gap-2 mb-2 border-b border-white/20 pb-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="font-bold text-sm">Gagal Menyimpan!</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 opacity-90">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button onclick="this.parentElement.parentElement.remove()" class="absolute top-2 right-2 text-white/70 hover:text-white">✕</button>
    </div>
</div>
@endif

<script>
    // Auto close setelah 5 detik
    setTimeout(() => {
        const flash = document.getElementById('flash-notification');
        if(flash) flash.remove();
        
        const val = document.getElementById('validation-notification');
        if(val) val.remove();
    }, 5000);
</script>

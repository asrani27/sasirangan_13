@php
    $whatsappNumber = '';
    if (isset($aduan) && !empty($aduan->nomor)) {
        $whatsappNumber = $aduan->nomor;
    } else {
        $dbAduan = \App\Models\NomorAduan::first();
        if ($dbAduan && !empty($dbAduan->nomor)) {
            $whatsappNumber = $dbAduan->nomor;
        }
    }
    $cleanNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
@endphp

@if(!empty($cleanNumber))
<aside aria-label="Layanan Aduan WhatsApp" class="fixed bottom-6 right-6 z-40 group">
    <a href="https://wa.me/{{ $cleanNumber }}?text=Halo%20Admin%20Dedikasi%2C%20saya%20ingin%20mengadukan%20informasi%20harga%20pangan"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 border-2 border-white focus:outline-hidden">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
        </span>
        <i class="fab fa-whatsapp text-2xl"></i>
        <div class="flex flex-col text-left pr-1">
            <span class="text-[10px] uppercase tracking-wider font-extrabold text-emerald-100">Layanan Aduan</span>
            <span class="text-sm font-bold">{{ $whatsappNumber }}</span>
        </div>
    </a>
</aside>
@endif

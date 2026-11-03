@php
    $toastSuccess = session('toast_success') ?? session('success');
    $toastError = session('toast_error') ?? session('error');
    $toastWarning = session('toast_warning') ?? session('warning');
    $toastInfo = session('toast_info') ?? session('info');
@endphp

@if($toastSuccess || $toastError || $toastWarning || $toastInfo)
<div class="fixed top-20 right-4 z-50 max-w-md w-full space-y-3 pointer-events-none p-4" id="toast-container">
    @if($toastSuccess)
        <div class="pointer-events-auto flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl shadow-lg transition duration-300">
            <i class="fas fa-circle-check text-emerald-600 text-xl mt-0.5"></i>
            <div class="flex-1 text-sm font-medium">{{ $toastSuccess }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fas fa-xmark"></i></button>
        </div>
    @endif
    @if($toastError)
        <div class="pointer-events-auto flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl shadow-lg transition duration-300">
            <i class="fas fa-circle-exclamation text-rose-600 text-xl mt-0.5"></i>
            <div class="flex-1 text-sm font-medium">{{ $toastError }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fas fa-xmark"></i></button>
        </div>
    @endif
    @if($toastWarning)
        <div class="pointer-events-auto flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl shadow-lg transition duration-300">
            <i class="fas fa-triangle-exclamation text-amber-600 text-xl mt-0.5"></i>
            <div class="flex-1 text-sm font-medium">{{ $toastWarning }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-800"><i class="fas fa-xmark"></i></button>
        </div>
    @endif
    @if($toastInfo)
        <div class="pointer-events-auto flex items-start gap-3 p-4 bg-sky-50 border border-sky-200 text-sky-900 rounded-2xl shadow-lg transition duration-300">
            <i class="fas fa-circle-info text-sky-600 text-xl mt-0.5"></i>
            <div class="flex-1 text-sm font-medium">{{ $toastInfo }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-sky-500 hover:text-sky-800"><i class="fas fa-xmark"></i></button>
        </div>
    @endif
</div>
<script>
    setTimeout(() => {
        const c = document.getElementById('toast-container');
        if (c) {
            c.style.transition = 'opacity 0.5s ease-out';
            c.style.opacity = '0';
            setTimeout(() => c.remove(), 500);
        }
    }, 5000);
</script>
@endif

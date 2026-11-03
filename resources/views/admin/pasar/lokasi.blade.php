@extends('layouts.admin')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #mapid { height: 440px; z-index: 1; }
</style>
@endpush

@section('title')
<div class="flex items-center gap-3">
    <a href="/data/pasar" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Titik Koordinat & Radius</h1>
        <p class="text-xs text-slate-500">Atur GPS dan batas radius survei pasar: {{ $edit->nama }}</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-map-pin text-emerald-600"></i>
                <span>Pengaturan Titik</span>
            </h3>

            <form action="/data/pasar/lokasi/{{ $edit->id }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pasar</label>
                    <input type="text" value="{{ $edit->nama }}" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-500 cursor-not-allowed">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Latitude</label>
                        <input type="text" id="lat" name="lat" value="{{ $edit->lat }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Longitude</label>
                        <input type="text" id="long" name="long" value="{{ $edit->long }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono font-semibold text-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Radius (Meter)</label>
                    <input type="number" id="radiusInput" name="radius" value="{{ $radius }}" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Batas validasi lokasi survei surveyor</p>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        <i class="fas fa-save"></i>
                        <span>Simpan Lokasi</span>
                    </button>
                    <a href="/data/pasar" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-xs text-slate-600 font-semibold">
                <span class="flex items-center gap-1.5"><i class="fas fa-crosshairs text-emerald-600"></i> Klik peta untuk geser titik</span>
                <span class="text-[11px] text-slate-400">OpenStreetMap</span>
            </div>
            <div id="mapid" class="w-full"></div>
        </div>
    </div>
</div>
@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const latlng = {!! json_encode($latlong) !!};
        let radius = Number({!! json_encode($radius) !!}) || 100;
        
        const map = L.map('mapid').setView([latlng.lat, latlng.lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([latlng.lat, latlng.lng]).addTo(map);
        let circle = L.circle([latlng.lat, latlng.lng], {
            color: '#059669',
            fillColor: '#10b981',
            fillOpacity: 0.25,
            radius: radius
        }).addTo(map);

        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            document.getElementById('lat').value = lat.toFixed(7);
            document.getElementById('long').value = lng.toFixed(7);
            
            if (marker) map.removeLayer(marker);
            if (circle) map.removeLayer(circle);

            marker = L.marker([lat, lng]).addTo(map);
            circle = L.circle([lat, lng], {
                color: '#059669',
                fillColor: '#10b981',
                fillOpacity: 0.25,
                radius: radius
            }).addTo(map);
        });

        const radiusInput = document.getElementById('radiusInput');
        radiusInput?.addEventListener('input', (e) => {
            radius = Number(e.target.value) || 100;
            if (circle) circle.setRadius(radius);
        });
    });
</script>
@endpush

@endsection

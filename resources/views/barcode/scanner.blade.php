@extends('layouts.app')
@section('title', 'Pindai Barcode / QR')
@section('subtitle', 'Cari barang secara instan lewat kamera perangkat')

@section('content')
<div class="max-w-xl mx-auto" x-data="scannerApp()" x-init="init()">
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <div id="reader" class="rounded-lg overflow-hidden"></div>

        <div class="mt-4 flex gap-2">
            <input type="text" x-model="manualCode" @keydown.enter.prevent="lookup(manualCode)" placeholder="Atau ketik kode barang secara manual…"
                class="flex-1 rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
            <button @click="lookup(manualCode)" class="text-sm font-semibold px-4 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Cari</button>
        </div>
    </div>

    <div x-show="result" x-cloak class="mt-4 bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <template x-if="result && result.found">
            <div>
                <p class="text-[11px] font-semibold uppercase text-moss-600">Barang Ditemukan</p>
                <p class="text-lg font-extrabold mt-1" x-text="result.item.name"></p>
                <p class="text-xs text-ink-900/40 font-mono" x-text="result.item.code"></p>
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="rounded-lg bg-ink-900/[0.03] p-3">
                        <p class="text-[10px] uppercase text-ink-900/40 font-semibold">Stok</p>
                        <p class="font-bold" x-text="result.item.stock + ' ' + result.item.unit"></p>
                    </div>
                    <div class="rounded-lg bg-ink-900/[0.03] p-3">
                        <p class="text-[10px] uppercase text-ink-900/40 font-semibold">Lokasi</p>
                        <p class="font-bold" x-text="result.item.location || '-'"></p>
                    </div>
                </div>
                <a :href="result.item.url" class="inline-flex mt-4 text-sm font-semibold px-4 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900">Lihat Detail Barang →</a>
            </div>
        </template>
        <template x-if="result && !result.found">
            <p class="text-sm text-rose-500 py-2">Barang dengan kode tersebut tidak ditemukan.</p>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    function scannerApp() {
        return {
            manualCode: '',
            result: null,
            scanner: null,

            init() {
                // Guard against double init (e.g. Alpine re-evaluating x-init,
                // or the browser restoring this page from bfcache on back/forward
                // navigation). Without this guard a second Html5QrcodeScanner
                // instance gets mounted on top of the first one inside #reader,
                // which is exactly what produces the duplicated "Select Camera (2)"
                // UI and the "NotFoundError: removeChild" crash when either
                // instance later tries to tear down DOM nodes it no longer owns.
                if (this.scanner) return;
                this.mount();

                // If the page is restored from bfcache (event.persisted === true),
                // the old scanner's DOM/state may be stale even though our JS
                // object still thinks it's initialized. Tear down and remount
                // cleanly instead of letting a second instance stack on top.
                window.addEventListener('pageshow', (event) => {
                    if (event.persisted) {
                        this.teardown();
                        this.mount();
                    }
                });
                window.addEventListener('pagehide', () => this.teardown());
            },

            mount() {
                const readerEl = document.getElementById('reader');
                if (readerEl) readerEl.innerHTML = '';
                this.scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 220 }, false);
                this.scanner.render((decodedText) => this.lookup(decodedText));
            },

            teardown() {
                if (!this.scanner) return;
                const instance = this.scanner;
                this.scanner = null;
                try {
                    instance.clear().catch(() => {});
                } catch (e) {
                    // Scanner was already in a torn-down state; nothing more to do.
                }
            },

            async lookup(code) {
                if (!code) return;
                const res = await fetch(`{{ route('barcode.lookup') }}?code=${encodeURIComponent(code)}`);
                this.result = res.ok ? await res.json() : { found: false };
            }
        }
    }
</script>
@endpush

<x-layouts.dashboard title="Profile" area="admin" active="profile">
    @php
        $value = static fn (string $key, ?string $fallback = null): ?string => old($key, $settings->get($key)?->meta['value'] ?? $fallback);
        $qris = $settings->get('qris');
        $qrisUrl = $qris?->image
            ? (\Illuminate\Support\Str::startsWith($qris->image, ['http://', 'https://', '/', 'images/', 'storage/'])
                ? asset(ltrim($qris->image, '/'))
                : \Illuminate\Support\Facades\Storage::url($qris->image))
            : null;
    @endphp

    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1fr_320px]">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <x-ui.panel heading="Kontak dan Sosial" description="Informasi ini dipakai ulang di halaman public dan flow pembayaran manual.">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-ui.textarea name="vision" label="Visi" rows="3" :value="$value('vision')" />
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.textarea name="mission" label="Misi" rows="4" :value="$value('mission')" />
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.textarea name="general_info" label="Informasi Umum" rows="4" :value="$value('general_info')" />
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.textarea name="address" label="Alamat" rows="3" :value="$value('address', 'Jl. S. Parman No. 202B, Padang')" />
                    </div>
                    <x-ui.field name="phone" label="Telepon" :value="$value('phone', '+62 812-0000-0000')" />
                    <x-ui.field name="whatsapp" label="WhatsApp" :value="$value('whatsapp')" />
                    <x-ui.field name="email" label="Email" type="email" :value="$value('email', 'hello@etcplanet.test')" />
                    <x-ui.field name="instagram" label="Instagram" :value="$value('instagram')" />
                    <x-ui.field name="hours" label="Jam Operasional" :value="$value('hours', 'Senin-Sabtu, 09.00-18.30')" />
                </div>
            </x-ui.panel>

            <x-ui.panel heading="Pembayaran">
                <div class="grid gap-5 md:grid-cols-2">
                    <x-ui.field name="bank_name" label="Nama Bank" :value="$value('bank_name')" />
                    <x-ui.field name="bank_account_number" label="Nomor Rekening" :value="$value('bank_account_number')" />
                    <div class="md:col-span-2">
                        <x-ui.field name="bank_account_name" label="Nama Pemilik Rekening" :value="$value('bank_account_name')" />
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.textarea name="payment_notes" label="Catatan Pembayaran" rows="4" :value="$value('payment_notes')" />
                    </div>
                </div>
            </x-ui.panel>
        </div>

        <aside class="space-y-5">
            <x-ui.panel heading="QRIS" description="Preview QRIS untuk pembayaran manual.">
                <div class="overflow-hidden rounded-box border-2 border-etc-outline-variant/60 bg-etc-surface p-3">
                    @if ($qrisUrl)
                        <img src="{{ $qrisUrl }}" alt="QRIS ETC Planet" class="aspect-square w-full rounded-selector object-contain">
                    @else
                        <div class="flex aspect-square items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                            <span class="material-symbols-outlined text-6xl">qr_code_2</span>
                        </div>
                    @endif
                </div>
                <div class="mt-4">
                    <x-ui.file-upload name="qris" label="Upload QRIS" accept="image/*" />
                </div>
            </x-ui.panel>

            <x-ui.button type="submit" icon="heroicon-m-check" class="w-full">Simpan Profile</x-ui.button>
        </aside>
    </form>
</x-layouts.dashboard>

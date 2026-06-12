<x-layouts.public title="Kontak" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar active="contact" />
    @php
        $defaultSubject = $selectedProgram ? 'Konsultasi program '.$selectedProgram->name : '';
        $defaultMessage = $selectedProgram
            ? 'Halo ETC Planet, saya ingin konsultasi tentang program '.$selectedProgram->name.'.'
            : '';
    @endphp

    <section class="public-section bg-etc-surface">
        <div class="public-shell grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="public-reveal" data-public-reveal>
                <p class="public-eyebrow">Kontak</p>
                <h1 class="public-title mt-4">Mulai konsultasi dengan ETC Planet</h1>
                <p class="public-subtitle mt-5">Kirim pertanyaan awal tentang program, jadwal, biaya, atau placement test. Tim ETC akan menghubungi kamu kembali.</p>

                <div class="mt-8 grid gap-3">
                    @foreach ([
                        ['icon' => 'location_on', 'label' => 'Alamat', 'value' => $settings['address'] ?? 'Jl. S. Parman No. 202B, Padang'],
                        ['icon' => 'call', 'label' => 'Telepon', 'value' => $settings['phone'] ?? '+62 812-0000-0000'],
                        ['icon' => 'mail', 'label' => 'Email', 'value' => $settings['email'] ?? 'hello@etcplanet.test'],
                        ['icon' => 'schedule', 'label' => 'Jam Operasional', 'value' => $settings['hours'] ?? 'Senin-Sabtu, 09.00-18.30'],
                    ] as $info)
                        <div class="public-card flex gap-4 p-4">
                            <span class="material-symbols-outlined text-2xl text-etc-magenta">{{ $info['icon'] }}</span>
                            <div>
                                <p class="font-heading text-sm font-bold">{{ $info['label'] }}</p>
                                <p class="mt-1 text-sm leading-6 text-etc-on-muted">{{ $info['value'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="public-card p-5 md:p-6 public-reveal" data-public-reveal>
                @if (session('status'))
                    <div class="mb-5">
                        <x-ui.alert status="success" title="Pesan terkirim">
                            {{ session('status') }}
                        </x-ui.alert>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.contact.store') }}" class="space-y-5">
                    @csrf

                    <x-ui.field name="name" label="Nama Lengkap" required />

                    <div class="grid gap-5 md:grid-cols-2">
                        <x-ui.email-field name="email" label="Email" required />
                        <x-ui.phone-field name="phone" label="No WhatsApp" />
                    </div>

                    <x-ui.field name="subject" label="Subjek" :value="$defaultSubject" />
                    <x-ui.textarea name="message" label="Pesan" :value="$defaultMessage" rows="6" required />

                    <x-ui.button type="submit" size="xl" class="w-full" icon="heroicon-m-paper-airplane" icon-position="after">
                        Kirim Pesan
                    </x-ui.button>
                </form>
            </div>
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>

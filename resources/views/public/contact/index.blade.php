<x-layouts.public title="Kontak">
    <section class="bg-[#fff8f8] py-20">
        <div class="mx-auto grid max-w-[1120px] gap-10 px-5 lg:grid-cols-[0.9fr_1.1fr] lg:px-0">
            <div>
                <p class="font-heading text-sm font-black uppercase tracking-[0.18em] text-etc-magenta">Kontak</p>
                <h1 class="mt-4 font-heading text-[42px] font-black leading-tight text-[#2a1820] md:text-[56px]">Mulai konsultasi dengan ETC Planet</h1>
                <p class="mt-5 text-[16px] leading-8 text-[#765f67]">Kirim pertanyaan awal tentang program, jadwal, biaya, atau placement test. Tim ETC akan menghubungi kamu kembali.</p>

                <div class="mt-8 space-y-4">
                    @foreach ([
                        ['icon' => 'location_on', 'label' => 'Alamat', 'value' => $settings['address'] ?? 'Jl. S. Parman No. 202B, Padang'],
                        ['icon' => 'call', 'label' => 'Telepon', 'value' => $settings['phone'] ?? '+62 812-0000-0000'],
                        ['icon' => 'mail', 'label' => 'Email', 'value' => $settings['email'] ?? 'hello@etcplanet.test'],
                        ['icon' => 'schedule', 'label' => 'Jam Operasional', 'value' => $settings['hours'] ?? 'Senin-Sabtu, 09.00-18.30'],
                    ] as $info)
                        <div class="flex gap-4 rounded-2xl border border-[#eeb8c9] bg-white p-5 shadow-soft">
                            <span class="material-symbols-outlined text-2xl text-etc-magenta">{{ $info['icon'] }}</span>
                            <div>
                                <p class="font-heading text-sm font-black text-[#2a1820]">{{ $info['label'] }}</p>
                                <p class="mt-1 text-sm leading-6 text-[#765f67]">{{ $info['value'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[24px] border border-[#eeb8c9] bg-white p-6 shadow-panel md:p-8">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('public.contact.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="font-heading text-sm font-bold text-[#2a1820]">Nama Lengkap</label>
                        <input id="name" name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border border-[#e2bdc7] px-4 py-3 outline-none focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10" required>
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="email" class="font-heading text-sm font-bold text-[#2a1820]">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-lg border border-[#e2bdc7] px-4 py-3 outline-none focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10" required>
                            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="font-heading text-sm font-bold text-[#2a1820]">No WhatsApp</label>
                            <input id="phone" name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border border-[#e2bdc7] px-4 py-3 outline-none focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">
                            @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="font-heading text-sm font-bold text-[#2a1820]">Subjek</label>
                        <input id="subject" name="subject" value="{{ old('subject') }}" class="mt-2 w-full rounded-lg border border-[#e2bdc7] px-4 py-3 outline-none focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10">
                        @error('subject') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="message" class="font-heading text-sm font-bold text-[#2a1820]">Pesan</label>
                        <textarea id="message" name="message" rows="6" class="mt-2 w-full rounded-lg border border-[#e2bdc7] px-4 py-3 outline-none focus:border-etc-magenta focus:ring-4 focus:ring-etc-magenta/10" required>{{ old('message') }}</textarea>
                        @error('message') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-etc-magenta px-8 py-3 font-heading text-sm font-bold text-white shadow-soft hover:bg-etc-primary">
                        Kirim Pesan
                        <span class="material-symbols-outlined text-base">send</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>

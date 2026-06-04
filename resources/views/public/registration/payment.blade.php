<x-layouts.public title="Pembayaran">
<link rel="stylesheet" href="{{ asset('css/pembayaran.css') }}">
<link rel="stylesheet" href="{{ asset('css/interactive.css') }}">

<header class="page-header">
    <h1>Pendaftaran ETC Planet</h1>
    <p>Selesaikan pembayaran awal, upload bukti, lalu konfirmasi agar admin bisa memverifikasi pendaftaran.</p>

    <div class="stepper" aria-label="Progress pendaftaran">
        <div class="step done">
            <div class="step-circle">&#10003;</div>
            <div class="step-label">Pilih Program</div>
        </div>
        <div class="step-line done"></div>
        <div class="step done">
            <div class="step-circle">&#10003;</div>
            <div class="step-label">Data Pribadi</div>
        </div>
        <div class="step-line done"></div>
        <div class="step active">
            <div class="step-circle">3</div>
            <div class="step-label">Pembayaran</div>
        </div>
        <div class="step-line"></div>
        <div class="step pending">
            <div class="step-circle">4</div>
            <div class="step-label">Konfirmasi</div>
        </div>
    </div>
</header>

@if (session('status'))
    <div class="payment-alert">{{ session('status') }}</div>
@endif

<main class="payment-layout">
    <aside class="summary-card">
        <h2>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 2h8v4H8z"/><path d="M6 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1"/><path d="M8 11h8M8 15h5"/></svg>
            Ringkasan Pesanan
        </h2>

        <div class="program-box">
            <span>Kode Pendaftaran</span>
            <strong>{{ $registration->registration_code }}</strong>
        </div>

        <div class="program-box">
            <span>Program</span>
            <strong>{{ $paymentSummary['program'] }}</strong>
        </div>

        <div class="summary-row"><span>Biaya Pendaftaran</span><strong>{{ $paymentSummary['registrationFee'] }}</strong></div>
        <div class="summary-row"><span>Biaya Program</span><strong>{{ $paymentSummary['programFee'] }}</strong></div>

        <div class="summary-total">
            <strong>Total</strong>
            <span>{{ $paymentSummary['total'] }}</span>
        </div>

        <div class="summary-note">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
            <p>Status akan tetap menunggu verifikasi sampai admin ETC mengecek bukti pembayaran.</p>
        </div>
    </aside>

    <section class="payment-content">
        <h2 class="section-title">Pilih Metode Pembayaran</h2>

        <div class="method-grid">
            <article class="method-card is-active" data-method="qris" onclick="selectMethod(this)">
                <div class="method-head">
                    <div class="method-icon qris-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4z"/><path d="M14 14h2v2h-2zM18 14h2v2h-2zM14 18h2v2h-2zM18 18h2v2h-2z"/></svg>
                    </div>
                    <div class="method-copy">
                        <h3>QRIS</h3>
                        <p>Scan dengan e-Wallet/M-Banking</p>
                    </div>
                    <span class="radio-dot"></span>
                </div>
                <div class="qris-box">
                    <div class="qr-panel">
                        <svg viewBox="0 0 78 78" aria-hidden="true">
                            <rect width="78" height="78" fill="white" rx="4"/>
                            <path fill="#e6007e" d="M8 8h18v18H8zM12 12v10h10V12H12Zm42-4h16v18H54zM58 12v10h8V12h-8ZM8 54h18v16H8zM12 58v8h10v-8H12Zm26-48h5v5h-5zm8 0h5v5h-5zM30 20h5v5h-5zm10 0h10v5H40zM30 31h5v5h-5zm10 0h5v5h-5zm10 0h20v5H50zM8 38h5v5H8zm10 0h8v5h-8zm18 0h8v5h-8zm13 0h5v5h-5zm9 0h12v5H58zM31 49h12v5H31zm18 0h5v5h-5zm9 0h12v5H58zM31 59h5v5h-5zm10 0h9v5h-9zm17 0h12v5H58zM31 68h16v5H31zm22 0h5v5h-5zm10 0h7v5h-7z"/>
                        </svg>
                    </div>
                    <span class="timer">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l3 2M9 2h6"/></svg>
                        Manual Check
                    </span>
                </div>
            </article>

            <article class="method-card" data-method="bank_transfer" onclick="selectMethod(this)">
                <div class="method-head">
                    <div class="method-icon bank-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10h18L12 4 3 10Z"/><path d="M5 10v8M9 10v8M15 10v8M19 10v8M3 20h18"/></svg>
                    </div>
                    <div class="method-copy">
                        <h3>Transfer Bank</h3>
                        <p>Transfer manual ke rekening</p>
                    </div>
                    <span class="radio-dot"></span>
                </div>
                <div class="bank-box">
                    <div class="bank-line"><span>Bank</span><strong>{{ $bankAccount['bank'] }}</strong></div>
                    <div class="bank-line">
                        <span>No. Rekening</span>
                        <div class="account-number">
                            <strong>{{ $bankAccount['number'] }}</strong>
                            <button type="button" onclick="event.stopPropagation(); copyText('{{ $bankAccount['number'] }}')" aria-label="Salin nomor rekening">
                                <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="bank-line"><span>A.N.</span><strong>{{ $bankAccount['holder'] }}</strong></div>
                </div>
            </article>
        </div>

        <form class="upload-card" method="POST" action="{{ route('registrations.payment.proof.store', ['registration' => $registration]) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="payment_method" class="payment-method-input" value="{{ old('payment_method', $registration->payment_method ?? 'qris') }}">

            <h2 class="section-title">Upload Bukti Pembayaran</h2>
            <div class="upload-zone" onclick="document.getElementById('fileInput').click()">
                <div class="upload-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a5 5 0 0 1 1.2-9.86A6 6 0 0 1 19 11.5 3.75 3.75 0 0 1 18.25 19H17"/><path d="M12 12v8"/><path d="M8.5 15.5 12 12l3.5 3.5"/></svg>
                </div>
                <p>Klik atau tarik file ke sini</p>
                <span>Mendukung JPG, PNG, atau PDF (Maks. 5MB)</span>
                <button type="button" onclick="event.stopPropagation(); document.getElementById('fileInput').click()">Pilih File</button>
            </div>
            <input type="file" name="payment_proof" id="fileInput" accept=".jpg,.jpeg,.png,.pdf" onchange="showFileName(this)" required>
            <div id="file-name" class="file-name"></div>
            @error('payment_proof') <small class="field-error">{{ $message }}</small> @enderror
            @error('payment_method') <small class="field-error">{{ $message }}</small> @enderror

            @if ($registration->payment_proof)
                <div class="proof-status">Bukti pembayaran sudah tersimpan. Upload ulang jika file sebelumnya salah.</div>
            @endif

            <button class="upload-submit" type="submit">Upload Bukti Pembayaran</button>
        </form>

        <form class="payment-actions" method="POST" action="{{ route('registrations.payment.confirm', ['registration' => $registration]) }}">
            @csrf
            <input type="hidden" name="payment_method" class="payment-method-input" value="{{ old('payment_method', $registration->payment_method ?? 'qris') }}">
            <label class="confirm-box" for="agree">
                <input type="checkbox" name="payment_confirmed" id="agree" value="1" required>
                <span>
                    <strong>Saya sudah membayar</strong>
                    Dengan mencentang, saya mengonfirmasi bahwa pembayaran telah berhasil dilakukan sejumlah tagihan.
                </span>
            </label>
            @error('payment_confirmed') <small class="field-error">{{ $message }}</small> @enderror

            <button class="confirm-button" type="submit">
                Konfirmasi Pembayaran
                <svg viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </button>
            <a class="cancel-button" href="{{ route('registrations.create', ['program' => $registration->program?->slug]) }}">Kembali ke Form</a>
        </form>
    </section>
</main>

@push('scripts')
<script>
function selectMethod(card) {
    document.querySelectorAll('.method-card').forEach(item => item.classList.remove('is-active'));
    card.classList.add('is-active');

    document.querySelectorAll('.payment-method-input').forEach((input) => {
        input.value = card.dataset.method;
    });
}

function copyText(text) {
    navigator.clipboard.writeText(text).then(() => alert('Nomor rekening disalin: ' + text));
}

function showFileName(input) {
    const fileName = document.getElementById('file-name');

    if (input.files.length > 0) {
        fileName.textContent = 'File dipilih: ' + input.files[0].name;
        fileName.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const currentMethod = document.querySelector('.payment-method-input')?.value || 'qris';
    const currentCard = document.querySelector(`.method-card[data-method="${currentMethod}"]`);

    if (currentCard) {
        selectMethod(currentCard);
    }
});
</script>
@endpush
</x-layouts.public>

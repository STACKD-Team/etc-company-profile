@php
    $programs = [
        [
            'name' => 'General English',
            'description' => 'Tingkatkan kemampuan komunikasi bahasa Inggris sehari-hari dengan metode interaktif.',
            'icon' => 'general-english',
            'tone' => 'icon-pink',
            'price' => 200000,
            'checked' => true,
        ],
        [
            'name' => 'TOEFL Preparation',
            'description' => 'Persiapan intensif untuk mencapai target skor TOEFL impianmu.',
            'icon' => 'toefl-preparation',
            'tone' => 'icon-dark',
            'price' => 250000,
            'checked' => false,
        ],
        [
            'name' => 'Bahasa Asia (Jepang/Korea)',
            'description' => 'Pelajari bahasa dan budaya Jepang atau Korea dari tingkat dasar.',
            'icon' => 'bahasa-asia',
            'tone' => 'icon-dark',
            'price' => 225000,
            'checked' => false,
        ],
        [
            'name' => 'Kids English',
            'description' => 'Belajar bahasa Inggris menyenangkan khusus untuk anak-anak.',
            'icon' => 'kids-english',
            'tone' => 'icon-dark',
            'price' => 180000,
            'checked' => false,
        ],
    ];

    $selectedProgram = collect($programs)->firstWhere('checked', true);
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Program - ETC Planet</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/pilih_program.css">
</head>

<body>
    @include('partials.header')

    <main class="registration-page">
        <section class="registration-shell" aria-labelledby="page-title">
            <div class="stepper" aria-label="Tahapan pendaftaran">
                <div class="stepper-item is-active">
                    <span class="stepper-number">1</span>
                    <span class="stepper-label">Pilih Program</span>
                </div>
                <div class="stepper-item">
                    <span class="stepper-number">2</span>
                    <span class="stepper-label">Data Pribadi</span>
                </div>
                <div class="stepper-item">
                    <span class="stepper-number">3</span>
                    <span class="stepper-label">Pembayaran</span>
                </div>
                <div class="stepper-item">
                    <span class="stepper-number">4</span>
                    <span class="stepper-label">Konfirmasi</span>
                </div>
            </div>

            <div class="registration-layout">
                <section class="program-panel">
                    <p class="eyebrow">Langkah 1 dari 4</p>
                    <h1 id="page-title">Pilih Program Belajar</h1>
                    <p class="page-subtitle">
                        Pilih program yang paling sesuai dengan tujuan belajarmu bersama ETC Planet.
                    </p>

                    <form class="program-form" action="#" method="POST">
                        <div class="program-grid">
                            @foreach ($programs as $program)
                                <label class="program-card">
                                    <input
                                        type="radio"
                                        name="program"
                                        value="{{ $program['name'] }}"
                                        data-name="{{ $program['name'] }}"
                                        data-icon="{{ $program['icon'] }}"
                                        data-tone="{{ $program['tone'] }}"
                                        data-price="{{ $program['price'] }}"
                                        @checked($program['checked'])
                                    >
                                    <span class="program-check" aria-hidden="true">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                    <span class="program-icon {{ $program['tone'] }}">
                                        @if ($program['icon'] === 'general-english')
                                            <svg class="program-svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M5.83333 18.6667C5.50278 18.6667 5.22569 18.5549 5.00208 18.3312C4.77847 18.1076 4.66667 17.8306 4.66667 17.5V15.1667H19.8333V4.66667H22.1667C22.4972 4.66667 22.7743 4.77847 22.9979 5.00208C23.2215 5.22569 23.3333 5.50278 23.3333 5.83333V23.3333L18.6667 18.6667H5.83333ZM0 17.5V1.16667C0 0.836111 0.111806 0.559028 0.335417 0.335417C0.559028 0.111806 0.836111 0 1.16667 0H16.3333C16.6639 0 16.941 0.111806 17.1646 0.335417C17.3882 0.559028 17.5 0.836111 17.5 1.16667V11.6667C17.5 11.9972 17.3882 12.2743 17.1646 12.4979C16.941 12.7215 16.6639 12.8333 16.3333 12.8333H4.66667L0 17.5Z" fill="#E6007F"/>
                                            </svg>
                                        @elseif ($program['icon'] === 'toefl-preparation')
                                            <svg class="program-svg-icon program-svg-icon-wide" width="26" height="21" viewBox="0 0 26 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M12.8333 21L4.66667 16.5667V9.56667L0 7L12.8333 0L25.6667 7V16.3333H23.3333V8.28333L21 9.56667V16.5667L12.8333 21ZM12.8333 11.3167L20.825 7L12.8333 2.68333L4.84167 7L12.8333 11.3167ZM12.8333 18.3458L18.6667 15.1958V10.7917L12.8333 14L7 10.7917V15.1958L12.8333 18.3458Z" fill="#5A3F47"/>
                                            </svg>
                                        @elseif ($program['icon'] === 'bahasa-asia')
                                            <svg class="program-svg-icon program-svg-icon-wide" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M12.7167 23.3333L18.025 9.33333H20.475L25.7833 23.3333H23.3333L22.0792 19.775H16.4208L15.1667 23.3333H12.7167ZM3.5 19.8333L1.86667 18.2L7.75833 12.3083C7.07778 11.6278 6.46042 10.85 5.90625 9.975C5.35208 9.1 4.84167 8.10833 4.375 7H6.825C7.21389 7.75833 7.60278 8.41945 7.99167 8.98333C8.38056 9.54722 8.84722 10.1111 9.39167 10.675C10.0333 10.0333 10.6993 9.13403 11.3896 7.97708C12.0799 6.82014 12.6 5.71667 12.95 4.66667H0V2.33333H8.16667V0H10.5V2.33333H18.6667V4.66667H15.2833C14.875 6.06667 14.2625 7.50556 13.4458 8.98333C12.6292 10.4611 11.8222 11.5889 11.025 12.3667L13.825 15.225L12.95 17.6167L9.39167 13.9708L3.5 19.8333ZM17.15 17.7333H21.35L19.25 11.7833L17.15 17.7333Z" fill="#5A3F47"/>
                                            </svg>
                                        @elseif ($program['icon'] === 'kids-english')
                                            <svg class="program-svg-icon program-svg-icon-small" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M13.4167 10.2083C13.0083 10.2083 12.6632 10.0674 12.3813 9.78542C12.0993 9.50347 11.9583 9.15833 11.9583 8.75C11.9583 8.34167 12.0993 7.99653 12.3813 7.71458C12.6632 7.43264 13.0083 7.29167 13.4167 7.29167C13.825 7.29167 14.1701 7.43264 14.4521 7.71458C14.734 7.99653 14.875 8.34167 14.875 8.75C14.875 9.15833 14.734 9.50347 14.4521 9.78542C14.1701 10.0674 13.825 10.2083 13.4167 10.2083ZM7.58333 10.2083C7.175 10.2083 6.82986 10.0674 6.54792 9.78542C6.26597 9.50347 6.125 9.15833 6.125 8.75C6.125 8.34167 6.26597 7.99653 6.54792 7.71458C6.82986 7.43264 7.175 7.29167 7.58333 7.29167C7.99167 7.29167 8.33681 7.43264 8.61875 7.71458C8.90069 7.99653 9.04167 8.34167 9.04167 8.75C9.04167 9.15833 8.90069 9.50347 8.61875 9.78542C8.33681 10.0674 7.99167 10.2083 7.58333 10.2083ZM10.5 16.3333C9.33333 16.3333 8.27847 16.0125 7.33542 15.3708C6.39236 14.7292 5.69722 13.8833 5.25 12.8333H15.75C15.3028 13.8833 14.6076 14.7292 13.6646 15.3708C12.7215 16.0125 11.6667 16.3333 10.5 16.3333ZM10.5 21C9.04167 21 7.67569 20.7229 6.40208 20.1688C5.12847 19.6146 4.02014 18.866 3.07708 17.9229C2.13403 16.9799 1.38542 15.8715 0.83125 14.5979C0.277083 13.3243 0 11.9583 0 10.5C0 9.04167 0.277083 7.67569 0.83125 6.40208C1.38542 5.12847 2.13403 4.02014 3.07708 3.07708C4.02014 2.13403 5.12847 1.38542 6.40208 0.83125C7.67569 0.277083 9.04167 0 10.5 0C11.9583 0 13.3243 0.277083 14.5979 0.83125C15.8715 1.38542 16.9799 2.13403 17.9229 3.07708C18.866 4.02014 19.6146 5.12847 20.1688 6.40208C20.7229 7.67569 21 9.04167 21 10.5C21 11.9583 20.7229 13.3243 20.1688 14.5979C19.6146 15.8715 18.866 16.9799 17.9229 17.9229C16.9799 18.866 15.8715 19.6146 14.5979 20.1688C13.3243 20.7229 11.9583 21 10.5 21ZM10.5 18.6667C12.7556 18.6667 14.6806 17.8694 16.275 16.275C17.8694 14.6806 18.6667 12.7556 18.6667 10.5C18.6667 8.24444 17.8694 6.31944 16.275 4.725C14.6806 3.13056 12.7556 2.33333 10.5 2.33333C10.3833 2.33333 10.2667 2.33333 10.15 2.33333C10.0333 2.33333 9.91667 2.35278 9.8 2.39167C9.68333 2.50833 9.60556 2.63472 9.56667 2.77083C9.52778 2.90694 9.50833 3.05278 9.50833 3.20833C9.50833 3.61667 9.64931 3.96181 9.93125 4.24375C10.2132 4.52569 10.5583 4.66667 10.9667 4.66667C11.1417 4.66667 11.3021 4.6375 11.4479 4.57917C11.5938 4.52083 11.7444 4.49167 11.9 4.49167C12.1333 4.49167 12.3278 4.57917 12.4833 4.75417C12.6389 4.92917 12.7167 5.13333 12.7167 5.36667C12.7167 5.81389 12.5076 6.10069 12.0896 6.22708C11.6715 6.35347 11.2972 6.41667 10.9667 6.41667C10.0917 6.41667 9.33819 6.10069 8.70625 5.46875C8.07431 4.83681 7.75833 4.08333 7.75833 3.20833C7.75833 3.15 7.75833 3.09167 7.75833 3.03333C7.75833 2.975 7.76806 2.89722 7.7875 2.8C6.17361 3.38333 4.86111 4.36528 3.85 5.74583C2.83889 7.12639 2.33333 8.71111 2.33333 10.5C2.33333 12.7556 3.13056 14.6806 4.725 16.275C6.31944 17.8694 8.24444 18.6667 10.5 18.6667Z" fill="#5A3F47"/>
                                            </svg>
                                        @else
                                            <i class="{{ $program['icon'] }}"></i>
                                        @endif
                                    </span>
                                    <span class="program-title">{{ $program['name'] }}</span>
                                    <span class="program-description">{{ $program['description'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </form>
                </section>

                <aside class="summary-card" aria-label="Ringkasan pendaftaran">
                    <div class="summary-header">
                        <h2>Ringkasan Pendaftaran</h2>
                    </div>

                    <div class="summary-body">
                        <p class="summary-label">Program Terpilih</p>
                        <div class="summary-program">
                            <span class="summary-icon {{ $selectedProgram['tone'] }}" id="summary-icon-wrap">
                                @if ($selectedProgram['icon'] === 'general-english')
                                    <svg class="program-svg-icon" id="summary-svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M5.83333 18.6667C5.50278 18.6667 5.22569 18.5549 5.00208 18.3312C4.77847 18.1076 4.66667 17.8306 4.66667 17.5V15.1667H19.8333V4.66667H22.1667C22.4972 4.66667 22.7743 4.77847 22.9979 5.00208C23.2215 5.22569 23.3333 5.50278 23.3333 5.83333V23.3333L18.6667 18.6667H5.83333ZM0 17.5V1.16667C0 0.836111 0.111806 0.559028 0.335417 0.335417C0.559028 0.111806 0.836111 0 1.16667 0H16.3333C16.6639 0 16.941 0.111806 17.1646 0.335417C17.3882 0.559028 17.5 0.836111 17.5 1.16667V11.6667C17.5 11.9972 17.3882 12.2743 17.1646 12.4979C16.941 12.7215 16.6639 12.8333 16.3333 12.8333H4.66667L0 17.5Z" fill="#E6007F"/>
                                    </svg>
                                @elseif ($selectedProgram['icon'] === 'toefl-preparation')
                                    <svg class="program-svg-icon program-svg-icon-wide" id="summary-svg-icon" width="26" height="21" viewBox="0 0 26 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12.8333 21L4.66667 16.5667V9.56667L0 7L12.8333 0L25.6667 7V16.3333H23.3333V8.28333L21 9.56667V16.5667L12.8333 21ZM12.8333 11.3167L20.825 7L12.8333 2.68333L4.84167 7L12.8333 11.3167ZM12.8333 18.3458L18.6667 15.1958V10.7917L12.8333 14L7 10.7917V15.1958L12.8333 18.3458Z" fill="#5A3F47"/>
                                    </svg>
                                @elseif ($selectedProgram['icon'] === 'bahasa-asia')
                                    <svg class="program-svg-icon program-svg-icon-wide" id="summary-svg-icon" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12.7167 23.3333L18.025 9.33333H20.475L25.7833 23.3333H23.3333L22.0792 19.775H16.4208L15.1667 23.3333H12.7167ZM3.5 19.8333L1.86667 18.2L7.75833 12.3083C7.07778 11.6278 6.46042 10.85 5.90625 9.975C5.35208 9.1 4.84167 8.10833 4.375 7H6.825C7.21389 7.75833 7.60278 8.41945 7.99167 8.98333C8.38056 9.54722 8.84722 10.1111 9.39167 10.675C10.0333 10.0333 10.6993 9.13403 11.3896 7.97708C12.0799 6.82014 12.6 5.71667 12.95 4.66667H0V2.33333H8.16667V0H10.5V2.33333H18.6667V4.66667H15.2833C14.875 6.06667 14.2625 7.50556 13.4458 8.98333C12.6292 10.4611 11.8222 11.5889 11.025 12.3667L13.825 15.225L12.95 17.6167L9.39167 13.9708L3.5 19.8333ZM17.15 17.7333H21.35L19.25 11.7833L17.15 17.7333Z" fill="#5A3F47"/>
                                    </svg>
                                @elseif ($selectedProgram['icon'] === 'kids-english')
                                    <svg class="program-svg-icon program-svg-icon-small" id="summary-svg-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M13.4167 10.2083C13.0083 10.2083 12.6632 10.0674 12.3813 9.78542C12.0993 9.50347 11.9583 9.15833 11.9583 8.75C11.9583 8.34167 12.0993 7.99653 12.3813 7.71458C12.6632 7.43264 13.0083 7.29167 13.4167 7.29167C13.825 7.29167 14.1701 7.43264 14.4521 7.71458C14.734 7.99653 14.875 8.34167 14.875 8.75C14.875 9.15833 14.734 9.50347 14.4521 9.78542C14.1701 10.0674 13.825 10.2083 13.4167 10.2083ZM7.58333 10.2083C7.175 10.2083 6.82986 10.0674 6.54792 9.78542C6.26597 9.50347 6.125 9.15833 6.125 8.75C6.125 8.34167 6.26597 7.99653 6.54792 7.71458C6.82986 7.43264 7.175 7.29167 7.58333 7.29167C7.99167 7.29167 8.33681 7.43264 8.61875 7.71458C8.90069 7.99653 9.04167 8.34167 9.04167 8.75C9.04167 9.15833 8.90069 9.50347 8.61875 9.78542C8.33681 10.0674 7.99167 10.2083 7.58333 10.2083ZM10.5 16.3333C9.33333 16.3333 8.27847 16.0125 7.33542 15.3708C6.39236 14.7292 5.69722 13.8833 5.25 12.8333H15.75C15.3028 13.8833 14.6076 14.7292 13.6646 15.3708C12.7215 16.0125 11.6667 16.3333 10.5 16.3333ZM10.5 21C9.04167 21 7.67569 20.7229 6.40208 20.1688C5.12847 19.6146 4.02014 18.866 3.07708 17.9229C2.13403 16.9799 1.38542 15.8715 0.83125 14.5979C0.277083 13.3243 0 11.9583 0 10.5C0 9.04167 0.277083 7.67569 0.83125 6.40208C1.38542 5.12847 2.13403 4.02014 3.07708 3.07708C4.02014 2.13403 5.12847 1.38542 6.40208 0.83125C7.67569 0.277083 9.04167 0 10.5 0C11.9583 0 13.3243 0.277083 14.5979 0.83125C15.8715 1.38542 16.9799 2.13403 17.9229 3.07708C18.866 4.02014 19.6146 5.12847 20.1688 6.40208C20.7229 7.67569 21 9.04167 21 10.5C21 11.9583 20.7229 13.3243 20.1688 14.5979C19.6146 15.8715 18.866 16.9799 17.9229 17.9229C16.9799 18.866 15.8715 19.6146 14.5979 20.1688C13.3243 20.7229 11.9583 21 10.5 21ZM10.5 18.6667C12.7556 18.6667 14.6806 17.8694 16.275 16.275C17.8694 14.6806 18.6667 12.7556 18.6667 10.5C18.6667 8.24444 17.8694 6.31944 16.275 4.725C14.6806 3.13056 12.7556 2.33333 10.5 2.33333C10.3833 2.33333 10.2667 2.33333 10.15 2.33333C10.0333 2.33333 9.91667 2.35278 9.8 2.39167C9.68333 2.50833 9.60556 2.63472 9.56667 2.77083C9.52778 2.90694 9.50833 3.05278 9.50833 3.20833C9.50833 3.61667 9.64931 3.96181 9.93125 4.24375C10.2132 4.52569 10.5583 4.66667 10.9667 4.66667C11.1417 4.66667 11.3021 4.6375 11.4479 4.57917C11.5938 4.52083 11.7444 4.49167 11.9 4.49167C12.1333 4.49167 12.3278 4.57917 12.4833 4.75417C12.6389 4.92917 12.7167 5.13333 12.7167 5.36667C12.7167 5.81389 12.5076 6.10069 12.0896 6.22708C11.6715 6.35347 11.2972 6.41667 10.9667 6.41667C10.0917 6.41667 9.33819 6.10069 8.70625 5.46875C8.07431 4.83681 7.75833 4.08333 7.75833 3.20833C7.75833 3.15 7.75833 3.09167 7.75833 3.03333C7.75833 2.975 7.76806 2.89722 7.7875 2.8C6.17361 3.38333 4.86111 4.36528 3.85 5.74583C2.83889 7.12639 2.33333 8.71111 2.33333 10.5C2.33333 12.7556 3.13056 14.6806 4.725 16.275C6.31944 17.8694 8.24444 18.6667 10.5 18.6667Z" fill="#5A3F47"/>
                                    </svg>
                                @else
                                    <i class="{{ $selectedProgram['icon'] }}" id="summary-icon"></i>
                                @endif
                            </span>
                            <strong id="summary-name">{{ $selectedProgram['name'] }}</strong>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-row">
                            <span>Biaya Pendaftaran</span>
                            <strong id="summary-price">Rp {{ number_format($selectedProgram['price'], 0, ',', '.') }}</strong>
                        </div>
                        <p class="summary-note">*Biaya program akan diinformasikan selanjutnya.</p>

                        <div class="summary-divider"></div>

                        <div class="summary-row summary-total">
                            <span>Total Sementara</span>
                            <strong id="summary-total">Rp {{ number_format($selectedProgram['price'], 0, ',', '.') }}</strong>
                        </div>

                        <a href="#" class="continue-button">
                            Lanjut ke Data Pribadi
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    @include('partials.footer')

    <script>
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        const summaryIconWrap = document.getElementById('summary-icon-wrap');
        const summaryName = document.getElementById('summary-name');
        const summaryPrice = document.getElementById('summary-price');
        const summaryTotal = document.getElementById('summary-total');
        const summaryCard = document.querySelector('.summary-card');
        const continueButton = document.querySelector('.continue-button');
        const stepperItems = document.querySelectorAll('.stepper-item');
        const selectedCards = document.querySelectorAll('.program-card');
        const generalEnglishIcon = `
            <svg class="program-svg-icon" id="summary-svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M5.83333 18.6667C5.50278 18.6667 5.22569 18.5549 5.00208 18.3312C4.77847 18.1076 4.66667 17.8306 4.66667 17.5V15.1667H19.8333V4.66667H22.1667C22.4972 4.66667 22.7743 4.77847 22.9979 5.00208C23.2215 5.22569 23.3333 5.50278 23.3333 5.83333V23.3333L18.6667 18.6667H5.83333ZM0 17.5V1.16667C0 0.836111 0.111806 0.559028 0.335417 0.335417C0.559028 0.111806 0.836111 0 1.16667 0H16.3333C16.6639 0 16.941 0.111806 17.1646 0.335417C17.3882 0.559028 17.5 0.836111 17.5 1.16667V11.6667C17.5 11.9972 17.3882 12.2743 17.1646 12.4979C16.941 12.7215 16.6639 12.8333 16.3333 12.8333H4.66667L0 17.5Z" fill="#E6007F"/>
            </svg>
        `;
        const toeflPreparationIcon = `
            <svg class="program-svg-icon program-svg-icon-wide" id="summary-svg-icon" width="26" height="21" viewBox="0 0 26 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12.8333 21L4.66667 16.5667V9.56667L0 7L12.8333 0L25.6667 7V16.3333H23.3333V8.28333L21 9.56667V16.5667L12.8333 21ZM12.8333 11.3167L20.825 7L12.8333 2.68333L4.84167 7L12.8333 11.3167ZM12.8333 18.3458L18.6667 15.1958V10.7917L12.8333 14L7 10.7917V15.1958L12.8333 18.3458Z" fill="#5A3F47"/>
            </svg>
        `;
        const bahasaAsiaIcon = `
            <svg class="program-svg-icon program-svg-icon-wide" id="summary-svg-icon" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12.7167 23.3333L18.025 9.33333H20.475L25.7833 23.3333H23.3333L22.0792 19.775H16.4208L15.1667 23.3333H12.7167ZM3.5 19.8333L1.86667 18.2L7.75833 12.3083C7.07778 11.6278 6.46042 10.85 5.90625 9.975C5.35208 9.1 4.84167 8.10833 4.375 7H6.825C7.21389 7.75833 7.60278 8.41945 7.99167 8.98333C8.38056 9.54722 8.84722 10.1111 9.39167 10.675C10.0333 10.0333 10.6993 9.13403 11.3896 7.97708C12.0799 6.82014 12.6 5.71667 12.95 4.66667H0V2.33333H8.16667V0H10.5V2.33333H18.6667V4.66667H15.2833C14.875 6.06667 14.2625 7.50556 13.4458 8.98333C12.6292 10.4611 11.8222 11.5889 11.025 12.3667L13.825 15.225L12.95 17.6167L9.39167 13.9708L3.5 19.8333ZM17.15 17.7333H21.35L19.25 11.7833L17.15 17.7333Z" fill="#5A3F47"/>
            </svg>
        `;
        const kidsEnglishIcon = `
            <svg class="program-svg-icon program-svg-icon-small" id="summary-svg-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M13.4167 10.2083C13.0083 10.2083 12.6632 10.0674 12.3813 9.78542C12.0993 9.50347 11.9583 9.15833 11.9583 8.75C11.9583 8.34167 12.0993 7.99653 12.3813 7.71458C12.6632 7.43264 13.0083 7.29167 13.4167 7.29167C13.825 7.29167 14.1701 7.43264 14.4521 7.71458C14.734 7.99653 14.875 8.34167 14.875 8.75C14.875 9.15833 14.734 9.50347 14.4521 9.78542C14.1701 10.0674 13.825 10.2083 13.4167 10.2083ZM7.58333 10.2083C7.175 10.2083 6.82986 10.0674 6.54792 9.78542C6.26597 9.50347 6.125 9.15833 6.125 8.75C6.125 8.34167 6.26597 7.99653 6.54792 7.71458C6.82986 7.43264 7.175 7.29167 7.58333 7.29167C7.99167 7.29167 8.33681 7.43264 8.61875 7.71458C8.90069 7.99653 9.04167 8.34167 9.04167 8.75C9.04167 9.15833 8.90069 9.50347 8.61875 9.78542C8.33681 10.0674 7.99167 10.2083 7.58333 10.2083ZM10.5 16.3333C9.33333 16.3333 8.27847 16.0125 7.33542 15.3708C6.39236 14.7292 5.69722 13.8833 5.25 12.8333H15.75C15.3028 13.8833 14.6076 14.7292 13.6646 15.3708C12.7215 16.0125 11.6667 16.3333 10.5 16.3333ZM10.5 21C9.04167 21 7.67569 20.7229 6.40208 20.1688C5.12847 19.6146 4.02014 18.866 3.07708 17.9229C2.13403 16.9799 1.38542 15.8715 0.83125 14.5979C0.277083 13.3243 0 11.9583 0 10.5C0 9.04167 0.277083 7.67569 0.83125 6.40208C1.38542 5.12847 2.13403 4.02014 3.07708 3.07708C4.02014 2.13403 5.12847 1.38542 6.40208 0.83125C7.67569 0.277083 9.04167 0 10.5 0C11.9583 0 13.3243 0.277083 14.5979 0.83125C15.8715 1.38542 16.9799 2.13403 17.9229 3.07708C18.866 4.02014 19.6146 5.12847 20.1688 6.40208C20.7229 7.67569 21 9.04167 21 10.5C21 11.9583 20.7229 13.3243 20.1688 14.5979C19.6146 15.8715 18.866 16.9799 17.9229 17.9229C16.9799 18.866 15.8715 19.6146 14.5979 20.1688C13.3243 20.7229 11.9583 21 10.5 21ZM10.5 18.6667C12.7556 18.6667 14.6806 17.8694 16.275 16.275C17.8694 14.6806 18.6667 12.7556 18.6667 10.5C18.6667 8.24444 17.8694 6.31944 16.275 4.725C14.6806 3.13056 12.7556 2.33333 10.5 2.33333C10.3833 2.33333 10.2667 2.33333 10.15 2.33333C10.0333 2.33333 9.91667 2.35278 9.8 2.39167C9.68333 2.50833 9.60556 2.63472 9.56667 2.77083C9.52778 2.90694 9.50833 3.05278 9.50833 3.20833C9.50833 3.61667 9.64931 3.96181 9.93125 4.24375C10.2132 4.52569 10.5583 4.66667 10.9667 4.66667C11.1417 4.66667 11.3021 4.6375 11.4479 4.57917C11.5938 4.52083 11.7444 4.49167 11.9 4.49167C12.1333 4.49167 12.3278 4.57917 12.4833 4.75417C12.6389 4.92917 12.7167 5.13333 12.7167 5.36667C12.7167 5.81389 12.5076 6.10069 12.0896 6.22708C11.6715 6.35347 11.2972 6.41667 10.9667 6.41667C10.0917 6.41667 9.33819 6.10069 8.70625 5.46875C8.07431 4.83681 7.75833 4.08333 7.75833 3.20833C7.75833 3.15 7.75833 3.09167 7.75833 3.03333C7.75833 2.975 7.76806 2.89722 7.7875 2.8C6.17361 3.38333 4.86111 4.36528 3.85 5.74583C2.83889 7.12639 2.33333 8.71111 2.33333 10.5C2.33333 12.7556 3.13056 14.6806 4.725 16.275C6.31944 17.8694 8.24444 18.6667 10.5 18.6667Z" fill="#5A3F47"/>
            </svg>
        `;
        const showToast = (() => {
            const toast = document.createElement('div');
            toast.className = 'registration-toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            document.body.appendChild(toast);

            let timer;

            return (message) => {
                toast.textContent = message;
                toast.classList.add('is-visible');
                clearTimeout(timer);
                timer = setTimeout(() => {
                    toast.classList.remove('is-visible');
                }, 2200);
            };
        })();

        const markSelectedCard = (radio) => {
            selectedCards.forEach((card) => {
                card.classList.toggle('is-selected', card.contains(radio));
            });
        };

        const flashSummary = () => {
            summaryCard.classList.remove('is-updated');
            void summaryCard.offsetWidth;
            summaryCard.classList.add('is-updated');
        };

        document.querySelectorAll('input[name="program"]').forEach((radio) => {
            if (radio.checked) {
                markSelectedCard(radio);
            }

            radio.addEventListener('change', () => {
                summaryIconWrap.className = `summary-icon ${radio.dataset.tone}`;
                const customIcons = {
                    'general-english': generalEnglishIcon,
                    'toefl-preparation': toeflPreparationIcon,
                    'bahasa-asia': bahasaAsiaIcon,
                    'kids-english': kidsEnglishIcon,
                };
                summaryIconWrap.innerHTML = customIcons[radio.dataset.icon] || `<i class="${radio.dataset.icon}" id="summary-icon"></i>`;
                summaryName.textContent = radio.dataset.name;

                const price = Number(radio.dataset.price);
                const formattedPrice = formatter.format(price).replace('IDR', 'Rp').trim();

                summaryPrice.textContent = formattedPrice;
                summaryTotal.textContent = formattedPrice;
                markSelectedCard(radio);
                flashSummary();
                showToast(`${radio.dataset.name} dipilih.`);
            });
        });

        continueButton?.addEventListener('click', (event) => {
            event.preventDefault();

            const selectedProgram = document.querySelector('input[name="program"]:checked')?.dataset.name || 'Program';
            continueButton.classList.add('is-loading');
            stepperItems[1]?.classList.add('is-preview');
            showToast(`Lanjut ke data pribadi untuk ${selectedProgram}.`);

            setTimeout(() => {
                continueButton.classList.remove('is-loading');
                stepperItems[1]?.classList.remove('is-preview');
            }, 1400);
        });
    </script>
</body>

</html>

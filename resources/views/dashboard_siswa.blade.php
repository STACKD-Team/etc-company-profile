<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - ETC Planet</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/dashboard_siswa.css">
</head>

<body>
    <div class="student-dashboard">
        <aside class="student-sidebar">
            <a href="#" class="sidebar-brand">ETC Planet</a>

            <div class="student-profile">
                <img src="/images/foto_profile.jpg" alt="Foto profil Budi">
                <div>
                    <strong>Student Portal</strong>
                    <span>ETC Planet Learner</span>
                </div>
            </div>

            <nav class="student-nav" aria-label="Navigasi dashboard siswa">
                <a href="#" class="nav-link is-active">
                    <svg class="nav-svg-icon nav-svg-icon-dashboard" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M10 6V0H18V6H10ZM0 10V0H8V10H0ZM10 18V8H18V18H10ZM0 18V12H8V18H0Z" fill="#E6007F"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-link">
                    <svg class="nav-svg-icon nav-svg-icon-profile" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8 8C6.9 8 5.95833 7.60833 5.175 6.825C4.39167 6.04167 4 5.1 4 4C4 2.9 4.39167 1.95833 5.175 1.175C5.95833 0.391667 6.9 0 8 0C9.1 0 10.0417 0.391667 10.825 1.175C11.6083 1.95833 12 2.9 12 4C12 5.1 11.6083 6.04167 10.825 6.825C10.0417 7.60833 9.1 8 8 8ZM0 16V13.2C0 12.6333 0.145833 12.1125 0.4375 11.6375C0.729167 11.1625 1.11667 10.8 1.6 10.55C2.63333 10.0333 3.68333 9.64583 4.75 9.3875C5.81667 9.12917 6.9 9 8 9C9.1 9 10.1833 9.12917 11.25 9.3875C12.3167 9.64583 13.3667 10.0333 14.4 10.55C14.8833 10.8 15.2708 11.1625 15.5625 11.6375C15.8542 12.1125 16 12.6333 16 13.2V16H0ZM2 14H14V13.2C14 13.0167 13.9542 12.85 13.8625 12.7C13.7708 12.55 13.65 12.4333 13.5 12.35C12.6 11.9 11.6917 11.5625 10.775 11.3375C9.85833 11.1125 8.93333 11 8 11C7.06667 11 6.14167 11.1125 5.225 11.3375C4.30833 11.5625 3.4 11.9 2.5 12.35C2.35 12.4333 2.22917 12.55 2.1375 12.7C2.04583 12.85 2 13.0167 2 13.2V14ZM8 6C8.55 6 9.02083 5.80417 9.4125 5.4125C9.80417 5.02083 10 4.55 10 4C10 3.45 9.80417 2.97917 9.4125 2.5875C9.02083 2.19583 8.55 2 8 2C7.45 2 6.97917 2.19583 6.5875 2.5875C6.19583 2.97917 6 3.45 6 4C6 4.55 6.19583 5.02083 6.5875 5.4125C6.97917 5.80417 7.45 6 8 6Z" fill="#A1A1AA"/>
                    </svg>
                    <span>Profil Saya</span>
                </a>
                <a href="#" class="nav-link">
                    <svg class="nav-svg-icon nav-svg-icon-class" width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M11 18L4 14.2V8.2L0 6L11 0L22 6V14H20V7.1L18 8.2V14.2L11 18ZM11 9.7L17.85 6L11 2.3L4.15 6L11 9.7ZM11 15.725L16 13.025V9.25L11 12L6 9.25V13.025L11 15.725Z" fill="#A1A1AA"/>
                    </svg>
                    <span>Kelas Saya</span>
                </a>
                <a href="#" class="nav-link">
                    <svg class="nav-svg-icon nav-svg-icon-report" width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6.85 14.825L10 12.925L13.15 14.85L12.325 11.25L15.1 8.85L11.45 8.525L10 5.125L8.55 8.5L4.9 8.825L7.675 11.25L6.85 14.825ZM3.825 19L5.45 11.975L0 7.25L7.2 6.625L10 0L12.8 6.625L20 7.25L14.55 11.975L16.175 19L10 15.275L3.825 19Z" fill="#A1A1AA"/>
                    </svg>
                    <span>Rapor</span>
                </a>
                <a href="#" class="nav-link">
                    <svg class="nav-svg-icon nav-svg-icon-payment" width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M13 9C12.1667 9 11.4583 8.70833 10.875 8.125C10.2917 7.54167 10 6.83333 10 6C10 5.16667 10.2917 4.45833 10.875 3.875C11.4583 3.29167 12.1667 3 13 3C13.8333 3 14.5417 3.29167 15.125 3.875C15.7083 4.45833 16 5.16667 16 6C16 6.83333 15.7083 7.54167 15.125 8.125C14.5417 8.70833 13.8333 9 13 9ZM6 12C5.45 12 4.97917 11.8042 4.5875 11.4125C4.19583 11.0208 4 10.55 4 10V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H20C20.55 0 21.0208 0.195833 21.4125 0.5875C21.8042 0.979167 22 1.45 22 2V10C22 10.55 21.8042 11.0208 21.4125 11.4125C21.0208 11.8042 20.55 12 20 12H6ZM8 10H18C18 9.45 18.1958 8.97917 18.5875 8.5875C18.9792 8.19583 19.45 8 20 8V4C19.45 4 18.9792 3.80417 18.5875 3.4125C18.1958 3.02083 18 2.55 18 2H8C8 2.55 7.80417 3.02083 7.4125 3.4125C7.02083 3.80417 6.55 4 6 4V8C6.55 8 7.02083 8.19583 7.4125 8.5875C7.80417 8.97917 8 9.45 8 10ZM19 16H2C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V3H2V14H19V16ZM6 10V2V10Z" fill="#A1A1AA"/>
                    </svg>
                    <span>Riwayat Pembayaran</span>
                </a>
            </nav>

            <a href="#" class="help-button">
                <svg class="help-svg-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7.4625 12C7.725 12 7.94688 11.9094 8.12813 11.7281C8.30937 11.5469 8.4 11.325 8.4 11.0625C8.4 10.8 8.30937 10.5781 8.12813 10.3969C7.94688 10.2156 7.725 10.125 7.4625 10.125C7.2 10.125 6.97813 10.2156 6.79688 10.3969C6.61562 10.5781 6.525 10.8 6.525 11.0625C6.525 11.325 6.61562 11.5469 6.79688 11.7281C6.97813 11.9094 7.2 12 7.4625 12ZM6.7875 9.1125H8.175C8.175 8.7 8.22188 8.375 8.31563 8.1375C8.40938 7.9 8.675 7.575 9.1125 7.1625C9.4375 6.8375 9.69375 6.52812 9.88125 6.23438C10.0688 5.94063 10.1625 5.5875 10.1625 5.175C10.1625 4.475 9.90625 3.9375 9.39375 3.5625C8.88125 3.1875 8.275 3 7.575 3C6.8625 3 6.28437 3.1875 5.84062 3.5625C5.39687 3.9375 5.0875 4.3875 4.9125 4.9125L6.15 5.4C6.2125 5.175 6.35313 4.93125 6.57188 4.66875C6.79063 4.40625 7.125 4.275 7.575 4.275C7.975 4.275 8.275 4.38438 8.475 4.60313C8.675 4.82188 8.775 5.0625 8.775 5.325C8.775 5.575 8.7 5.80937 8.55 6.02812C8.4 6.24687 8.2125 6.45 7.9875 6.6375C7.4375 7.125 7.1 7.49375 6.975 7.74375C6.85 7.99375 6.7875 8.45 6.7875 9.1125ZM7.5 15C6.4625 15 5.4875 14.8031 4.575 14.4094C3.6625 14.0156 2.86875 13.4812 2.19375 12.8062C1.51875 12.1312 0.984375 11.3375 0.590625 10.425C0.196875 9.5125 0 8.5375 0 7.5C0 6.4625 0.196875 5.4875 0.590625 4.575C0.984375 3.6625 1.51875 2.86875 2.19375 2.19375C2.86875 1.51875 3.6625 0.984375 4.575 0.590625C5.4875 0.196875 6.4625 0 7.5 0C8.5375 0 9.5125 0.196875 10.425 0.590625C11.3375 0.984375 12.1312 1.51875 12.8062 2.19375C13.4812 2.86875 14.0156 3.6625 14.4094 4.575C14.8031 5.4875 15 6.4625 15 7.5C15 8.5375 14.8031 9.5125 14.4094 10.425C14.0156 11.3375 13.4812 12.1312 12.8062 12.8062C12.1312 13.4812 11.3375 14.0156 10.425 14.4094C9.5125 14.8031 8.5375 15 7.5 15ZM7.5 13.5C9.175 13.5 10.5938 12.9188 11.7563 11.7563C12.9188 10.5938 13.5 9.175 13.5 7.5C13.5 5.825 12.9188 4.40625 11.7563 3.24375C10.5938 2.08125 9.175 1.5 7.5 1.5C5.825 1.5 4.40625 2.08125 3.24375 3.24375C2.08125 4.40625 1.5 5.825 1.5 7.5C1.5 9.175 2.08125 10.5938 3.24375 11.7563C4.40625 12.9188 5.825 13.5 7.5 13.5Z" fill="#D4D4D8"/>
                </svg>
                Bantuan
            </a>
        </aside>

        <main class="student-main">
            <section class="welcome-banner" aria-labelledby="welcome-title">
                <div class="welcome-content">
                    <h1 id="welcome-title">Halo, Budi! <span>&#128075;</span></h1>
                    <p>Selamat datang kembali di ETC Planet. Mari lanjutkan progres belajarmu hari ini.</p>
                </div>
            </section>

            <section class="stats-grid" aria-label="Ringkasan progres belajar">
                <article class="stat-card">
                    <span class="stat-icon icon-pink">
                        <svg class="stat-svg-icon stat-svg-icon-class" width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M13 5.9V4.2C13.55 3.96667 14.1125 3.79167 14.6875 3.675C15.2625 3.55833 15.8667 3.5 16.5 3.5C16.9333 3.5 17.3583 3.53333 17.775 3.6C18.1917 3.66667 18.6 3.75 19 3.85V5.45C18.6 5.3 18.1958 5.1875 17.7875 5.1125C17.3792 5.0375 16.95 5 16.5 5C15.8667 5 15.2583 5.07917 14.675 5.2375C14.0917 5.39583 13.5333 5.61667 13 5.9ZM13 11.4V9.7C13.55 9.46667 14.1125 9.29167 14.6875 9.175C15.2625 9.05833 15.8667 9 16.5 9C16.9333 9 17.3583 9.03333 17.775 9.1C18.1917 9.16667 18.6 9.25 19 9.35V10.95C18.6 10.8 18.1958 10.6875 17.7875 10.6125C17.3792 10.5375 16.95 10.5 16.5 10.5C15.8667 10.5 15.2583 10.575 14.675 10.725C14.0917 10.875 13.5333 11.1 13 11.4ZM13 8.65V6.95C13.55 6.71667 14.1125 6.54167 14.6875 6.425C15.2625 6.30833 15.8667 6.25 16.5 6.25C16.9333 6.25 17.3583 6.28333 17.775 6.35C18.1917 6.41667 18.6 6.5 19 6.6V8.2C18.6 8.05 18.1958 7.9375 17.7875 7.8625C17.3792 7.7875 16.95 7.75 16.5 7.75C15.8667 7.75 15.2583 7.82917 14.675 7.9875C14.0917 8.14583 13.5333 8.36667 13 8.65ZM12 13.05C12.7333 12.7 13.4708 12.4375 14.2125 12.2625C14.9542 12.0875 15.7167 12 16.5 12C17.1 12 17.6875 12.05 18.2625 12.15C18.8375 12.25 19.4167 12.4 20 12.6V2.7C19.45 2.46667 18.8792 2.29167 18.2875 2.175C17.6958 2.05833 17.1 2 16.5 2C15.7167 2 14.9417 2.1 14.175 2.3C13.4083 2.5 12.6833 2.8 12 3.2V13.05ZM11 16C10.2 15.3667 9.33333 14.875 8.4 14.525C7.46667 14.175 6.5 14 5.5 14C4.8 14 4.1125 14.0917 3.4375 14.275C2.7625 14.4583 2.11667 14.7167 1.5 15.05C1.15 15.2333 0.8125 15.225 0.4875 15.025C0.1625 14.825 0 14.5333 0 14.15V2.1C0 1.91667 0.0458333 1.74167 0.1375 1.575C0.229167 1.40833 0.366667 1.28333 0.55 1.2C1.33333 0.816667 2.1375 0.520833 2.9625 0.3125C3.7875 0.104167 4.63333 0 5.5 0C6.46667 0 7.4125 0.125 8.3375 0.375C9.2625 0.625 10.15 1 11 1.5C11.85 1 12.7375 0.625 13.6625 0.375C14.5875 0.125 15.5333 0 16.5 0C17.3667 0 18.2125 0.104167 19.0375 0.3125C19.8625 0.520833 20.6667 0.816667 21.45 1.2C21.6333 1.28333 21.7708 1.40833 21.8625 1.575C21.9542 1.74167 22 1.91667 22 2.1V14.15C22 14.5333 21.8375 14.825 21.5125 15.025C21.1875 15.225 20.85 15.2333 20.5 15.05C19.8833 14.7167 19.2375 14.4583 18.5625 14.275C17.8875 14.0917 17.2 14 16.5 14C15.5 14 14.5333 14.175 13.6 14.525C12.6667 14.875 11.8 15.3667 11 16Z" fill="#E6007F"/>
                        </svg>
                    </span>
                    <p>Kelas Aktif</p>
                    <strong>2</strong>
                </article>

                <article class="stat-card">
                    <span class="stat-icon icon-green">
                        <svg class="stat-svg-icon stat-svg-icon-meeting" width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M7.95 16.35L4.4 12.8L5.85 11.35L7.95 13.45L12.15 9.25L13.6 10.7L7.95 16.35ZM2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V4C0 3.45 0.195833 2.97917 0.5875 2.5875C0.979167 2.19583 1.45 2 2 2H3V0H5V2H13V0H15V2H16C16.55 2 17.0208 2.19583 17.4125 2.5875C17.8042 2.97917 18 3.45 18 4V18C18 18.55 17.8042 19.0208 17.4125 19.4125C17.0208 19.8042 16.55 20 16 20H2ZM2 18H16V8H2V18Z" fill="#008A18"/>
                        </svg>
                    </span>
                    <p>Total Pertemuan</p>
                    <strong>24</strong>
                </article>

                <article class="stat-card">
                    <span class="stat-icon icon-pink">
                        <svg class="stat-svg-icon stat-svg-icon-grade" width="10" height="20" viewBox="0 0 10 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M0 0H10V7.85C10 8.23333 9.91667 8.575 9.75 8.875C9.58333 9.175 9.35 9.41667 9.05 9.6L5.5 11.7L6.2 14H10L6.9 16.2L8.1 20L5 17.65L1.9 20L3.1 16.2L0 14H3.8L4.5 11.7L0.95 9.6C0.65 9.41667 0.416667 9.175 0.25 8.875C0.0833333 8.575 0 8.23333 0 7.85V0ZM4 2V9.05L5 9.65L6 9.05V2H4Z" fill="#B90065"/>
                        </svg>
                    </span>
                    <p>Rata-rata Nilai</p>
                    <strong>A-</strong>
                </article>

                <article class="stat-card">
                    <span class="stat-icon icon-gray">
                        <svg class="stat-svg-icon stat-svg-icon-certificate" width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V4C0 3.45 0.195833 2.97917 0.5875 2.5875C0.979167 2.19583 1.45 2 2 2H6.2C6.41667 1.4 6.77917 0.916667 7.2875 0.55C7.79583 0.183333 8.36667 0 9 0C9.63333 0 10.2042 0.183333 10.7125 0.55C11.2208 0.916667 11.5833 1.4 11.8 2H16C16.55 2 17.0208 2.19583 17.4125 2.5875C17.8042 2.97917 18 3.45 18 4V18C18 18.55 17.8042 19.0208 17.4125 19.4125C17.0208 19.8042 16.55 20 16 20H2ZM4 16H11V14H4V16ZM4 12H14V10H4V12ZM4 8H14V6H4V8ZM9 3.25C9.21667 3.25 9.39583 3.17917 9.5375 3.0375C9.67917 2.89583 9.75 2.71667 9.75 2.5C9.75 2.28333 9.67917 2.10417 9.5375 1.9625C9.39583 1.82083 9.21667 1.75 9 1.75C8.78333 1.75 8.60417 1.82083 8.4625 1.9625C8.32083 2.10417 8.25 2.28333 8.25 2.5C8.25 2.71667 8.32083 2.89583 8.4625 3.0375C8.60417 3.17917 8.78333 3.25 9 3.25Z" fill="#656464"/>
                        </svg>
                    </span>
                    <p>Sertifikat</p>
                    <strong>1</strong>
                </article>
            </section>

            <section class="dashboard-lower">
                <div class="course-column">
                    <div class="section-title">
                        <svg class="section-svg-icon section-svg-icon-course" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M7.5 14.5L14.5 10L7.5 5.5V14.5ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="#E6007F"/>
                        </svg>
                        <h2>Kelas Berlangsung</h2>
                    </div>

                    <article class="course-card">
                        <div class="course-image">
                            <span class="course-tag">ENGLISH</span>
                            <img src="/images/foto_english_student.jpg" alt="Ilustrasi siswa belajar bahasa Inggris">
                        </div>

                        <div class="course-info">
                            <h3>General English - Intermediate B1</h3>
                            <div class="teacher-row">
                                <img src="/images/profile_sarah.jpg" alt="Foto Ms. Sarah Jenkins">
                                <span>Ms. Sarah Jenkins</span>
                            </div>

                            <div class="progress-label">
                                <span>Progres: Pertemuan 12/20</span>
                                <strong>60%</strong>
                            </div>
                            <div class="progress-track">
                                <span></span>
                            </div>

                            <div class="course-footer">
                                <p>
                                    <svg class="course-date-svg" width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M1.33333 13.3333C0.966667 13.3333 0.652778 13.2028 0.391667 12.9417C0.130556 12.6806 0 12.3667 0 12V2.66667C0 2.3 0.130556 1.98611 0.391667 1.725C0.652778 1.46389 0.966667 1.33333 1.33333 1.33333H2V0H3.33333V1.33333H8.66667V0H10V1.33333H10.6667C11.0333 1.33333 11.3472 1.46389 11.6083 1.725C11.8694 1.98611 12 2.3 12 2.66667V12C12 12.3667 11.8694 12.6806 11.6083 12.9417C11.3472 13.2028 11.0333 13.3333 10.6667 13.3333H1.33333ZM1.33333 12H10.6667V5.33333H1.33333V12ZM1.33333 4H10.6667V2.66667H1.33333V4ZM1.33333 4V2.66667V4Z" fill="#5A3F47"/>
                                    </svg>
                                    Selasa, 14:00 WIB
                                </p>
                                <a href="#" class="join-button">
                                    Gabung Sesi
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>

                <aside class="report-column">
                    <div class="section-title report-title">
                        <svg class="section-svg-icon section-svg-icon-report-download" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M8 12L3 7L4.4 5.55L7 8.15V0H9V8.15L11.6 5.55L13 7L8 12ZM2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V11H2V14H14V11H16V14C16 14.55 15.8042 15.0208 15.4125 15.4125C15.0208 15.8042 14.55 16 14 16H2Z" fill="#5F5E5E"/>
                        </svg>
                        <h2>Rapor Terakhir</h2>
                    </div>

                    <article class="report-card">
                        <div class="report-item">
                            <span class="report-icon icon-pink">
                                <svg class="report-svg-icon report-svg-icon-term-primary" width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M4 16H12V14H4V16ZM4 12H12V10H4V12ZM2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0H10L16 6V18C16 18.55 15.8042 19.0208 15.4125 19.4125C15.0208 19.8042 14.55 20 14 20H2ZM9 7V2H2V18H14V7H9ZM2 2V7V2V7V18V2Z" fill="#E6007F"/>
                                </svg>
                            </span>
                            <div>
                                <strong>Term 1 - 2024</strong>
                                <p>General English</p>
                            </div>
                            <a href="#" aria-label="Unduh rapor Term 1 2024"><i class="fa-solid fa-download"></i></a>
                        </div>

                        <div class="report-item">
                            <span class="report-icon icon-gray">
                                <svg class="report-svg-icon report-svg-icon-term-secondary" width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M4 16H12V14H4V16ZM4 12H12V10H4V12ZM2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0H10L16 6V18C16 18.55 15.8042 19.0208 15.4125 19.4125C15.0208 19.8042 14.55 20 14 20H2ZM9 7V2H2V18H14V7H9ZM2 2V7V2V7V18V2Z" fill="#656464"/>
                                </svg>
                            </span>
                            <div>
                                <strong>Term 4 - 2023</strong>
                                <p>TOEFL Prep</p>
                            </div>
                            <a href="#" aria-label="Unduh rapor Term 4 2023"><i class="fa-solid fa-download"></i></a>
                        </div>

                        <a href="#" class="all-report-button">Lihat Semua Rapor</a>
                    </article>
                </aside>
            </section>
        </main>
    </div>

    <script>
        const showToast = (() => {
            const toast = document.createElement('div');
            toast.className = 'dashboard-toast';
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

        document.querySelectorAll('.student-nav .nav-link').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();

                document.querySelectorAll('.student-nav .nav-link').forEach((item) => {
                    item.classList.remove('is-active');
                });

                link.classList.add('is-active');
            });
        });

        document.querySelectorAll('.stat-card').forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('is-visible');
            }, index * 120);
        });

        document.querySelectorAll('.stat-card strong').forEach((value) => {
            const target = Number(value.textContent);

            if (!Number.isFinite(target)) {
                return;
            }

            let current = 0;
            const step = Math.max(1, Math.ceil(target / 24));
            value.textContent = '0';

            const counter = setInterval(() => {
                current = Math.min(target, current + step);
                value.textContent = current;

                if (current === target) {
                    clearInterval(counter);
                }
            }, 28);
        });

        document.querySelectorAll('.progress-track span').forEach((bar) => {
            const targetWidth = getComputedStyle(bar).width;
            const parentWidth = bar.parentElement.getBoundingClientRect().width || 1;
            const targetPercent = `${Math.round((parseFloat(targetWidth) / parentWidth) * 100)}%`;

            bar.style.width = '0';

            requestAnimationFrame(() => {
                bar.style.width = targetPercent;
            });
        });

        document.querySelector('.join-button')?.addEventListener('click', (event) => {
            event.preventDefault();
            showToast('Membuka sesi kelas...');
        });

        document.querySelectorAll('.report-item a').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                showToast('Rapor sedang disiapkan.');
            });
        });
    </script>
</body>

</html>

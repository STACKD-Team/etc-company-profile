<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\Response;

class RegistrationReceiptController extends Controller
{
    public function download(Registration $registration, RegistrationService $registrations): Response
    {
        $receipt = $registrations->receiptData($registration);
        $filename = 'bukti-pendaftaran-'.$receipt['code'].'.html';

        return response($this->html($receipt), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    protected function html(array $receipt): string
    {
        $rows = [
            'Kode Pendaftaran' => $receipt['code'],
            'Nama Siswa' => $receipt['student'],
            'Email' => $receipt['email'],
            'No. HP' => $receipt['phone'],
            'Program' => $receipt['program'],
            'Jadwal Hari' => $receipt['preferred_days'] ?? '-',
            'Jadwal Jam' => $receipt['preferred_time'] ?? '-',
            'Metode Pembayaran' => $receipt['payment_method'] ?? 'Belum dikonfirmasi',
            'Total Tagihan' => 'Rp '.number_format((float) $receipt['payment_amount'], 0, ',', '.'),
            'Status' => $receipt['status'],
            'Tanggal Submit' => optional($receipt['submitted_at'])->format('d M Y H:i'),
        ];

        $body = collect($rows)
            ->map(fn ($value, $label) => '<tr><th>'.e($label).'</th><td>'.e((string) $value).'</td></tr>')
            ->implode('');

        return '<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Bukti Pendaftaran ETC Planet</title>
<style>
body{font-family:Arial,sans-serif;color:#27171c;margin:40px;background:#fff8f8}
.sheet{max-width:760px;margin:auto;background:white;border:1px solid #e2bdc7;border-radius:16px;padding:32px}
h1{color:#e6007f;margin:0 0 8px}
p{color:#5a3f47}
table{width:100%;border-collapse:collapse;margin-top:24px}
th,td{text-align:left;border-bottom:1px solid #f1d4de;padding:12px}
th{width:34%;color:#5a3f47}
.note{margin-top:24px;padding:16px;border-radius:12px;background:#ffe8ed}
</style>
</head>
<body>
<main class="sheet">
<h1>Bukti Pendaftaran ETC Planet</h1>
<p>Dokumen digital ini menjadi bukti bahwa formulir pendaftaran online sudah diterima.</p>
<table>'.$body.'</table>
<div class="note">Pembayaran akan diproses setelah admin ETC Planet memverifikasi bukti pembayaran.</div>
</main>
</body>
</html>';
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Services\ContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const SETTINGS = [
        'address' => 'Alamat',
        'phone' => 'Telepon',
        'email' => 'Email',
        'instagram' => 'Instagram',
        'hours' => 'Jam Operasional',
        'bank_name' => 'Nama Bank',
        'bank_account_name' => 'Nama Pemilik Rekening',
        'bank_account_number' => 'Nomor Rekening',
        'payment_notes' => 'Catatan Pembayaran',
        'qris' => 'QRIS',
    ];

    public function __construct(private ContentService $contents) {}

    public function index(): View
    {
        return view('admin.settings.index', [
            'settings' => $this->contents->settings(array_keys(self::SETTINGS)),
            'labels' => self::SETTINGS,
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['qris']);

        $this->contents->updateSettings($data, self::SETTINGS, $request->file('qris'));

        return to_route('admin.settings.index')->with('status', 'Settings berhasil diperbarui.');
    }
}

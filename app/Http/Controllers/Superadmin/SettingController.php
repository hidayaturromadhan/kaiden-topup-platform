<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'midtrans_server_key' => Setting::get('midtrans_server_key', env('MIDTRANS_SERVER_KEY', '')),
            'midtrans_client_key' => Setting::get('midtrans_client_key', env('MIDTRANS_CLIENT_KEY', '')),
            'midtrans_is_production' => Setting::get('midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', 'false')),
            'digiflazz_username' => Setting::get('digiflazz_username', env('DIGIFLAZZ_USERNAME', '')),
            'digiflazz_api_key' => Setting::get('digiflazz_api_key', env('DIGIFLAZZ_API_KEY', '')),
            'digiflazz_base_url' => Setting::get('digiflazz_base_url', env('DIGIFLAZZ_BASE_URL', 'https://api.digiflazz.com/v1')),
            'digiflazz_low_balance_threshold' => Setting::get('digiflazz_low_balance_threshold', '500000'),
            'ign_checker_enabled' => Setting::get('ign_checker_enabled', 'true'),
            'whatsapp_number' => Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')),
        ];

        return view('superadmin.settings.index', compact('settings'));
    }

    /**
     * Memperbarui Kredensial Integrasi & Feature Flag IGN
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'midtrans_server_key' => ['nullable', 'string', 'max:255'],
            'midtrans_client_key' => ['nullable', 'string', 'max:255'],
            'midtrans_is_production' => ['required', 'in:true,false'],
            'digiflazz_username' => ['nullable', 'string', 'max:255'],
            'digiflazz_api_key' => ['nullable', 'string', 'max:255'],
            'digiflazz_base_url' => ['required', 'url'],
            'digiflazz_low_balance_threshold' => ['required', 'integer', 'min:0'],
            'ign_checker_enabled' => ['required', 'in:true,false'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
        ]);

        $oldSettings = [
            'midtrans_is_production' => Setting::get('midtrans_is_production'),
            'digiflazz_low_balance_threshold' => Setting::get('digiflazz_low_balance_threshold'),
            'ign_checker_enabled' => Setting::get('ign_checker_enabled'),
            'whatsapp_number' => Setting::get('whatsapp_number'),
        ];

        // Simpan setting sensitif (dienkripsi jika di DB sesuai RULES.md Section 3 no. 9)
        if (!empty($validated['midtrans_server_key'])) {
            Setting::set('midtrans_server_key', $validated['midtrans_server_key'], true, 'Midtrans Server Key');
        }
        if (!empty($validated['midtrans_client_key'])) {
            Setting::set('midtrans_client_key', $validated['midtrans_client_key'], false, 'Midtrans Client Key');
        }

        Setting::set('midtrans_is_production', $validated['midtrans_is_production'], false, 'Midtrans Production Mode');

        if (!empty($validated['digiflazz_username'])) {
            Setting::set('digiflazz_username', $validated['digiflazz_username'], false, 'Digiflazz Username');
        }
        if (!empty($validated['digiflazz_api_key'])) {
            Setting::set('digiflazz_api_key', $validated['digiflazz_api_key'], true, 'Digiflazz API Key');
        }

        Setting::set('digiflazz_base_url', $validated['digiflazz_base_url'], false, 'Digiflazz Base URL');
        Setting::set('digiflazz_low_balance_threshold', (string)$validated['digiflazz_low_balance_threshold'], false, 'Threshold Saldo Rendah');
        Setting::set('ign_checker_enabled', $validated['ign_checker_enabled'], false, 'Feature Flag IGN Checker');

        if (!empty($validated['whatsapp_number'])) {
            $cleanNumber = preg_replace('/[^0-9]/', '', $validated['whatsapp_number']);
            Setting::set('whatsapp_number', $cleanNumber, false, 'Nomor WhatsApp resmi Customer Support untuk wa.me');
        }

        // RULES.md Section 4.C.2: Wajib mencatat audit log perubahan konfigurasi API & feature flag
        AuditLog::record(
            action: 'integrasi.atur',
            targetType: 'Setting',
            targetId: null,
            oldValues: $oldSettings,
            newValues: [
                'midtrans_is_production' => $validated['midtrans_is_production'],
                'digiflazz_low_balance_threshold' => $validated['digiflazz_low_balance_threshold'],
                'ign_checker_enabled' => $validated['ign_checker_enabled'],
                'keys_updated' => [
                    'midtrans_server_key' => !empty($validated['midtrans_server_key']),
                    'digiflazz_api_key' => !empty($validated['digiflazz_api_key']),
                ]
            ],
            actorId: $user->id
        );

        return back()->with('success', 'Konfigurasi integrasi dan feature flag berhasil diperbarui.');
    }
}


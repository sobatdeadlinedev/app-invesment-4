<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = [
            'app_name'         => Config::get('app_name', ['value' => '']),
            'app_logo'         => Config::get('app_logo', ['value' => '']),
            'app_announcement' => Config::get('app_announcement', ['value' => '']),
            'app_wallet_trc20' => Config::get('app_wallet_trc20', [
                'name'      => 'TRON Network (TRC20)',
                'addresses' => [''],
            ]),
            'app_wallet_bep20' => Config::get('app_wallet_bep20', [
                'name'      => 'Binance Smart Chain (BEP20)',
                'addresses' => [''],
            ]),
        ];

        // Backward-compat: kalau masih simpan key 'address' (string lama), convert ke array
        foreach (['app_wallet_trc20', 'app_wallet_bep20'] as $key) {
            if (isset($configs[$key]['address']) && !isset($configs[$key]['addresses'])) {
                $configs[$key]['addresses'] = array_filter([$configs[$key]['address']]);
                if (empty($configs[$key]['addresses'])) {
                    $configs[$key]['addresses'] = [''];
                }
            }
            if (empty($configs[$key]['addresses'])) {
                $configs[$key]['addresses'] = [''];
            }
        }

        return view('admin.pages.config.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'              => 'required|string|max:255',
            'app_announcement'      => 'nullable|string',
            'wallet_trc20_addresses'   => 'required|array|min:1|max:10',
            'wallet_trc20_addresses.*' => 'required|string|max:255',
            'wallet_bep20_addresses'   => 'required|array|min:1|max:10',
            'wallet_bep20_addresses.*' => 'required|string|max:255',
            'app_logo'              => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            Config::set('app_name', ['value' => $request->app_name]);
            Config::set('app_announcement', ['value' => $request->app_announcement]);

            Config::set('app_wallet_trc20', [
                'name'      => 'TRON Network (TRC20)',
                'addresses' => array_values(array_filter($request->wallet_trc20_addresses)),
            ]);

            Config::set('app_wallet_bep20', [
                'name'      => 'Binance Smart Chain (BEP20)',
                'addresses' => array_values(array_filter($request->wallet_bep20_addresses)),
            ]);

            if ($request->hasFile('app_logo')) {
                $oldLogo = Config::get('app_logo', ['value' => '']);
                if (!empty($oldLogo['value'])) {
                    $this->deleteOldFile($oldLogo['value']);
                }
                $logoUrl = $this->uploadFile($request->file('app_logo'), 'logo');
                Config::set('app_logo', ['value' => $logoUrl]);
            }

            return redirect()->route('admin.config.index')
                ->with('success', 'Configuration updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update configuration: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function uploadFile($file, $prefix)
    {
        $filename = $prefix . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('configs', $filename, 'public');
        return Storage::url($path);
    }

    private function deleteOldFile($url)
    {
        try {
            $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            // silent fail
        }
    }
}
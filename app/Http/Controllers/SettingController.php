<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        // Lấy tất cả setting
        $rawSettings = Setting::pluck('value', 'key')->all();

        // Xử lý riêng cho banner: Nếu là JSON thì decode thành mảng, nếu không thì để mảng rỗng
        if (isset($rawSettings['banner'])) {
            $rawSettings['banner'] = json_decode($rawSettings['banner'], true) ?: [];
        } else {
            $rawSettings['banner'] = [];
        }

        return Inertia::render('Settings/index', ['settings' => $rawSettings]);
    }

    public function update(Request $request)
    {
        // Lấy các input, bao gồm cả mảng banner gửi từ Vue lên
        $inputs = $request->only(['site_name', 'logo', 'banner', 'footer_text']);

        foreach ($inputs as $key => $value) {
            // Nếu key là banner, chúng ta chuyển mảng thành chuỗi JSON để lưu vào DB
            if ($key === 'banner' && is_array($value)) {
                $value = json_encode($value);
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('message', 'Cập nhật thành công!');
    }

    public function getSettings()
    {
        $settings = Setting::whereIn('key', ['site_name', 'logo', 'banner', 'footer_text'])
            ->pluck('value', 'key')
            ->toArray();

        // Decode banner trước khi trả về API cho Client
        if (isset($settings['banner'])) {
            $settings['banner'] = json_decode($settings['banner'], true) ?: [];
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}

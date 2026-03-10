<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Quan trọng: Dùng để gọi API
use Inertia\Inertia;

class SettingController extends Controller
{
    protected $allowKeys = ['site_name', 'logo', 'banner', 'favicon', 'footer_text'];

    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return Inertia::render('Settings/index', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $inputs = $request->only(['site_name', 'logo', 'banner', 'footer_text']);

        foreach ($inputs as $key => $value) {
            // Vì $value bây giờ đã là URL (string) từ FE gửi lên, 
            // ta chỉ cần lưu trực tiếp, không cần xử lý Storage:: nữa.
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('message', 'Cập nhật thành công!');
    }
    public function getSettings()
    {
        // Lấy các trường cần thiết cho client
        $settings = Setting::whereIn('key', ['site_name', 'logo', 'banner', 'footer_text'])
            ->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}

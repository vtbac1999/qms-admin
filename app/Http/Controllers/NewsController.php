<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    // Hiển thị danh sách tin tức
    public function index()
    {
        $news = News::latest()->paginate(10);
        return Inertia::render('News/Index', [
            'news' => $news
        ]);
    }

    // Trang tạo mới
    public function create()
    {
        return Inertia::render('News/Create');
    }

    // Lưu tin tức
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        News::create($data);

        return redirect()->route('news.index')->with('message', 'Tạo tin tức thành công!');
    }
}
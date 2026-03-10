<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Banner;


class BannerController extends Controller
{
    public function index() {
    return Inertia::render('Banners/Index', [
        'banners' => Banner::all()
    ]);
}
}

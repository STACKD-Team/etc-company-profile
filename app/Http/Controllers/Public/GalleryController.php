<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicDiscoveryService;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(PublicDiscoveryService $discovery): View
    {
        return view('public.gallery.index', [
            'galleryItems' => $discovery->gallery(),
        ]);
    }
}

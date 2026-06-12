<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicDiscoveryService;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(PublicDiscoveryService $discovery): View
    {
        return view('pages.public.faq.index', [
            'page' => $discovery->page('faq'),
            'faqs' => $discovery->faqItems(),
        ]);
    }
}

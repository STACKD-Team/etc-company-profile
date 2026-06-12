<?php

return [
    'navbar_main_items' => [
        ['label' => 'Beranda', 'route' => 'public.home', 'url' => '/', 'key' => 'home', 'icon' => 'home'],
        ['label' => 'Program', 'route' => 'public.programs.index', 'url' => '/programs', 'key' => 'program', 'icon' => 'school'],
        ['label' => 'Reels', 'route' => 'public.reels.index', 'url' => '/reels', 'key' => 'reels', 'icon' => 'smart_display'],
        ['label' => 'Kontak', 'route' => 'public.contact.index', 'url' => '/contact', 'key' => 'contact', 'icon' => 'call'],
    ],
    'navbar_more_items' => [
        ['label' => 'Tentang ETC', 'route' => 'public.about', 'url' => '/about', 'key' => 'about', 'icon' => 'info'],
        ['label' => 'Team', 'route' => 'public.team.index', 'url' => '/team', 'key' => 'team', 'icon' => 'groups'],
        ['label' => 'Fasilitas', 'route' => 'public.facilities.index', 'url' => '/facilities', 'key' => 'facilities', 'icon' => 'meeting_room'],
        ['label' => 'Galeri', 'route' => 'public.gallery.index', 'url' => '/gallery', 'key' => 'gallery', 'icon' => 'photo_library'],
        ['label' => 'FAQ', 'route' => 'public.faq.index', 'url' => '/faq', 'key' => 'faq', 'icon' => 'help'],
    ],
];

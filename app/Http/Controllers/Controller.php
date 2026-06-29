<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

abstract class Controller
{
    public function __call(string $method, array $parameters): View|RedirectResponse|Response
    {
        $request = request();

        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return $this->renderFoundationPage($request, $method);
        }

        return $this->handleFoundationAction($request);
    }

    protected function renderFoundationPage(Request $request, string $method): View|Response
    {
        $routeName = (string) $request->route()?->getName();

        if ($routeName === 'public.home') {
            return view('pages.public.home');
        }

        $area = $this->areaFromRoute($routeName);

        return view($this->viewForRoute($routeName, $area), [
            'title' => $this->titleFromRoute($routeName, $method),
            'area' => $area,
            'routeName' => $routeName,
        ]);
    }

    protected function handleFoundationAction(Request $request): RedirectResponse|Response
    {
        $routeName = (string) $request->route()?->getName();

        $this->validateFoundationAction($request, $routeName);

        if (Str::startsWith($routeName, 'public.chatbot.') || Str::contains($routeName, ['views.store', 'likes.store'])) {
            return response()->json([
                'status' => 'accepted',
                'message' => 'Permintaan diterima. Implementasi penuh akan ditambahkan pada modul fitur.',
            ]);
        }

        return redirect()
            ->route($this->fallbackRouteForAction($routeName))
            ->with('status', 'Aksi fondasi berhasil diterima. Implementasi penuh akan ditambahkan pada modul fitur.');
    }

    protected function validateFoundationAction(Request $request, string $routeName): void
    {
        $rules = match ($routeName) {
            'public.contact.store' => [
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:150'],
                'phone' => ['nullable', 'string', 'max:20'],
                'subject' => ['nullable', 'string', 'max:200'],
                'message' => ['required', 'string', 'max:5000'],
            ],
            'public.chatbot.messages.store' => [
                'session_id' => ['nullable', 'string', 'max:64'],
                'message' => ['required', 'string', 'max:1000'],
            ],
            default => [],
        };

        $request->validate($rules);
    }

    protected function areaFromRoute(string $routeName): string
    {
        return match (true) {
            Str::startsWith($routeName, 'admin.') => 'admin',
            default => 'public',
        };
    }

    protected function viewForRoute(string $routeName, string $area): string
    {
        $candidate = $area.'.placeholder';

        return view()->exists($candidate) ? $candidate : 'public.placeholder';
    }

    protected function titleFromRoute(string $routeName, string $method): string
    {
        $title = Str::of($routeName)
            ->replace(['public.', 'admin.'], '')
            ->replace(['.', '-'], ' ')
            ->headline()
            ->toString();

        return $title !== '' ? $title : Str::headline($method);
    }

    protected function fallbackRouteForAction(string $routeName): string
    {
        return match (true) {
            Str::startsWith($routeName, 'admin.') => 'admin.reel.index',
            default => 'public.home',
        };
    }
}

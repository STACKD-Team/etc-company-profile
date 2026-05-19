<?php

namespace App\Services;

use App\Models\Content;
use App\Models\Program;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PublicDiscoveryService
{
    public function page(string $slug): ?Content
    {
        return Content::query()
            ->where('type', 'page')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }

    /**
     * @return Collection<int, Content>
     */
    public function rooms(): Collection
    {
        return $this->publishedContent('room')->get();
    }

    /**
     * @return Collection<int, Content>
     */
    public function gallery(): Collection
    {
        return $this->publishedContent('gallery')->get();
    }

    /**
     * @return Collection<int, Reel>
     */
    public function reels(?int $limit = null): Collection
    {
        $query = Reel::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->latest();

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * @return Collection<int, User>
     */
    public function instructors(?int $limit = null): Collection
    {
        $query = User::query()
            ->instructors()
            ->where('is_active', true)
            ->where('show_on_team_page', true)
            ->orderBy('name');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * @return Collection<int, Program>
     */
    public function programHighlights(int $limit = 3): Collection
    {
        return Program::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return Content::query()
            ->where('type', 'setting')
            ->where('is_published', true)
            ->get()
            ->mapWithKeys(function (Content $content): array {
                $value = $content->meta['value'] ?? $content->body ?? null;

                return [$content->slug ?: Str::slug($content->title) => $value];
            })
            ->all();
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    public function faqItems(): array
    {
        $page = $this->page('faq');
        $items = $page?->meta['items'] ?? [];

        if (is_array($items) && $items !== []) {
            return array_values(array_filter($items, fn ($item): bool => is_array($item) && isset($item['question'], $item['answer'])));
        }

        return [
            [
                'question' => 'Bagaimana cara mendaftar?',
                'answer' => 'Hubungi tim ETC Planet melalui form kontak. Tim kami akan membantu memilih program dan jadwal yang sesuai.',
            ],
            [
                'question' => 'Apakah placement test dilakukan online?',
                'answer' => 'Placement test tetap dilakukan offline di ETC Planet agar hasil penempatan kelas lebih akurat.',
            ],
        ];
    }

    /**
     * @return array<string, int|string>
     */
    public function stats(): array
    {
        return [
            'students' => User::query()->students()->count(),
            'instructors' => User::query()->instructors()->where('is_active', true)->count(),
            'programs' => Program::query()->where('is_active', true)->count(),
            'satisfaction' => '98%',
        ];
    }

    /**
     * @return array{intent: string, reply: string}
     */
    public function answerChatbot(string $message): array
    {
        $text = Str::of($message)->lower()->toString();
        $settings = $this->settings();

        if (Str::contains($text, ['harga', 'biaya', 'bayar', 'qris', 'transfer'])) {
            return [
                'intent' => 'pricing',
                'reply' => 'Biaya pendaftaran ETC Planet mulai dari Rp 200.000. Biaya program bergantung pada kelas yang dipilih, dan tim kami bisa memberi rincian setelah kamu memilih program.',
            ];
        }

        if (Str::contains($text, ['daftar', 'pendaftaran', 'registrasi', 'join'])) {
            return [
                'intent' => 'registration',
                'reply' => 'Untuk Sprint 1, pendaftaran dibantu dulu lewat form kontak. Kirim nama, nomor WhatsApp, dan program yang diminati, lalu tim ETC akan menghubungi kamu.',
            ];
        }

        if (Str::contains($text, ['jadwal', 'hari', 'jam', 'schedule'])) {
            return [
                'intent' => 'schedule',
                'reply' => 'Jadwal umum tersedia Senin-Sabtu dengan beberapa pilihan jam belajar. Jika jadwal reguler tidak cocok, kamu bisa request schedule saat konsultasi.',
            ];
        }

        if (Str::contains($text, ['program', 'kelas', 'toefl', 'ielts', 'english', 'kids', 'teen'])) {
            $programs = $this->programHighlights()->pluck('name')->implode(', ');

            return [
                'intent' => 'program',
                'reply' => $programs !== ''
                    ? 'Program yang sedang tersedia antara lain '.$programs.'. Kamu bisa konsultasi dulu agar level dan target belajarmu lebih pas.'
                    : 'ETC Planet menyediakan kelas bahasa untuk anak, remaja, dewasa, private, dan persiapan TOEFL/TOEIC/IELTS.',
            ];
        }

        if (Str::contains($text, ['alamat', 'lokasi', 'kontak', 'wa', 'whatsapp', 'instagram'])) {
            return [
                'intent' => 'contact',
                'reply' => 'ETC Planet berlokasi di '.($settings['address'] ?? 'Jl. S. Parman No. 202B, Padang').'. Kamu juga bisa menghubungi '.($settings['phone'] ?? '+62 812-0000-0000').' untuk konsultasi cepat.',
            ];
        }

        return [
            'intent' => 'general',
            'reply' => 'Halo! Aku bisa bantu jawab tentang program, biaya, jadwal, pendaftaran, placement test, dan kontak ETC Planet.',
        ];
    }

    protected function publishedContent(string $type)
    {
        return Content::query()
            ->where('type', $type)
            ->where('is_published', true)
            ->orderBy('display_order')
            ->orderBy('title');
    }
}

<?php

namespace App\Services;

use App\Models\Content;
use App\Models\CourseClass;
use App\Models\Program;
use App\Models\Reel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Str;

class PublicChatbotDataService
{
    private const DATASETS = [
        'programs',
        'classes',
        'rooms',
        'partners',
        'gallery',
        'testimonials',
        'faq',
        'profile',
        'contact',
        'instructors',
        'reels',
    ];

    /**
     * @param array<int, string> $requested
     * @return array{items: array<int, array<string, mixed>>, links: array<int, array{label: string, url: string}>}
     */
    public function contextFor(string $message, array $requested = []): array
    {
        $datasets = $this->datasetsFor($message, $requested);
        $items = [];
        $links = [];

        foreach ($datasets as $dataset) {
            foreach ($this->datasetItems($dataset) as $item) {
                $items[] = $item;

                if (isset($item['link_label'], $item['url'])) {
                    $links[] = [
                        'label' => (string) $item['link_label'],
                        'url' => (string) $item['url'],
                    ];
                }
            }
        }

        return [
            'items' => $items,
            'links' => $this->uniqueLinks($links),
        ];
    }

    /**
     * @param array<int, string> $requested
     * @return array<int, string>
     */
    protected function datasetsFor(string $message, array $requested): array
    {
        $message = Str::of($message)->lower()->toString();
        $datasets = collect($requested)
            ->map(fn (mixed $dataset): string => strtolower((string) $dataset))
            ->filter(fn (string $dataset): bool => in_array($dataset, self::DATASETS, true));

        $keywordMap = [
            'programs' => ['program', 'kursus', 'kelas bahasa', 'english', 'inggris', 'toefl', 'ielts', 'toeic', 'mandarin', 'japanese', 'biaya', 'harga', 'pembayaran', 'promo', 'daftar'],
            'classes' => ['kelas', 'jadwal', 'hari', 'jam', 'schedule', 'ruang', 'placement'],
            'rooms' => ['ruang', 'ruangan', 'fasilitas', 'kelas nyaman', 'kapasitas'],
            'partners' => ['partner', 'mitra', 'kerja sama'],
            'gallery' => ['galeri', 'foto', 'kegiatan', 'dokumentasi'],
            'testimonials' => ['testimoni', 'ulasan', 'review', 'kepuasan'],
            'faq' => ['faq', 'tanya', 'pertanyaan', 'bagaimana'],
            'profile' => ['profil', 'tentang', 'sejarah', 'visi', 'misi', 'etc planet'],
            'contact' => ['alamat', 'lokasi', 'kontak', 'wa', 'whatsapp', 'instagram', 'telepon', 'email'],
            'instructors' => ['instruktur', 'pengajar', 'teacher', 'tutor', 'mentor'],
            'reels' => ['reels', 'video', 'konten'],
        ];

        foreach ($keywordMap as $dataset => $keywords) {
            if (Str::contains($message, $keywords)) {
                $datasets->push($dataset);
            }
        }

        if ($datasets->isNotEmpty()) {
            $datasets->push('faq');
        }

        if ($datasets->isEmpty()) {
            $datasets = collect(['programs', 'faq', 'profile', 'contact']);
        }

        return $datasets->unique()->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function datasetItems(string $dataset): array
    {
        return match ($dataset) {
            'programs' => $this->programs(),
            'classes' => $this->classes(),
            'rooms' => $this->rooms(),
            'partners' => $this->contents(Content::TYPE_PARTNER, 'Partner', route('public.home', [], false)),
            'gallery' => $this->contents(Content::TYPE_GALLERY, 'Galeri', route('public.gallery.index', [], false)),
            'testimonials' => $this->contents(Content::TYPE_TESTIMONIAL, 'Testimoni', route('public.home', [], false)),
            'faq' => $this->contents(Content::TYPE_FAQ, 'FAQ', route('public.faq.index', [], false)),
            'profile', 'contact' => $this->profileAndContact(),
            'instructors' => $this->instructors(),
            'reels' => $this->reels(),
            default => [],
        };
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function programs(): array
    {
        return Program::query()
            ->with('activePromotions')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(function (Program $program): array {
                $promotion = $program->currentPromotion();

                return [
                    'type' => 'program',
                    'title' => $program->name,
                    'category' => $program->category,
                    'program_type' => $program->type,
                    'target_age' => $program->target_age,
                    'description' => Str::limit(strip_tags((string) $program->description), 220),
                    'duration_meetings' => $program->duration_meetings,
                    'max_students' => $program->max_students,
                    'price' => $this->rupiah((float) $program->price),
                    'registration_fee' => $this->rupiah((float) $program->registration_fee),
                    'promotion' => $promotion?->displayBadge(),
                    'url' => route('public.programs.show', $program, false),
                    'link_label' => $program->name,
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function classes(): array
    {
        return CourseClass::query()
            ->with(['program:id,name,slug,is_active', 'room:id,name'])
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->whereHas('program', fn ($query) => $query->where('is_active', true))
            ->orderByRaw("CASE WHEN status = 'ongoing' THEN 0 ELSE 1 END")
            ->orderBy('start_date')
            ->limit(6)
            ->get()
            ->map(fn (CourseClass $class): array => [
                'type' => 'class',
                'title' => $class->name,
                'program' => $class->program?->name,
                'status' => $class->status,
                'schedule_days' => $class->schedule_days,
                'schedule_time' => $class->schedule_time,
                'room' => $class->room_label,
                'url' => $class->program ? route('public.programs.show', $class->program, false) : route('public.programs.index', [], false),
                'link_label' => $class->program?->name ?? 'Lihat Program',
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function rooms(): array
    {
        return Room::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->limit(6)
            ->get()
            ->map(fn (Room $room): array => [
                'type' => 'room',
                'title' => $room->name,
                'description' => Str::limit(strip_tags((string) $room->description), 180),
                'capacity' => $room->capacity,
                'facilities' => $room->facilities,
                'url' => route('public.facilities.index', [], false),
                'link_label' => 'Lihat Fasilitas',
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function contents(string $type, string $label, string $url): array
    {
        return Content::query()
            ->where('type', $type)
            ->where('is_published', true)
            ->orderBy('display_order')
            ->orderBy('title')
            ->limit(6)
            ->get()
            ->map(fn (Content $content): array => [
                'type' => $type,
                'title' => $content->title,
                'body' => Str::limit(strip_tags((string) $content->body), 260),
                'meta' => $content->meta,
                'url' => $url,
                'link_label' => $label,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function profileAndContact(): array
    {
        return Content::query()
            ->where('type', Content::TYPE_PROFILE)
            ->where('is_published', true)
            ->orderBy('display_order')
            ->orderBy('title')
            ->limit(8)
            ->get()
            ->map(fn (Content $content): array => [
                'type' => 'profile',
                'title' => $content->title,
                'slug' => $content->slug,
                'body' => Str::limit(strip_tags((string) $content->body), 320),
                'meta' => $content->meta,
                'url' => in_array($content->slug, ['address', 'phone', 'whatsapp', 'instagram', 'email'], true)
                    ? route('public.contact.index', [], false)
                    : route('public.about', [], false),
                'link_label' => in_array($content->slug, ['address', 'phone', 'whatsapp', 'instagram', 'email'], true)
                    ? 'Kontak ETC'
                    : 'Tentang ETC',
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function instructors(): array
    {
        return User::query()
            ->instructors()
            ->where('is_active', true)
            ->where('show_on_team_page', true)
            ->orderBy('name')
            ->limit(6)
            ->get()
            ->map(fn (User $user): array => [
                'type' => 'instructor',
                'title' => $user->full_name ?: $user->name,
                'position' => $user->instructor_position,
                'specialization' => $user->instructor_specialization,
                'bio' => Str::limit(strip_tags((string) $user->instructor_bio), 220),
                'url' => route('public.team.index', [], false),
                'link_label' => 'Lihat Team',
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function reels(): array
    {
        return Reel::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Reel $reel): array => [
                'type' => 'reel',
                'title' => $reel->title,
                'description' => Str::limit(strip_tags((string) $reel->description), 180),
                'category' => $reel->category,
                'url' => route('public.reels.index', ['reel' => $reel->id], false),
                'link_label' => $reel->title,
            ])
            ->all();
    }

    protected function rupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * @param array<int, array{label: string, url: string}> $links
     * @return array<int, array{label: string, url: string}>
     */
    protected function uniqueLinks(array $links): array
    {
        return collect($links)
            ->filter(fn (array $link): bool => filled($link['label'] ?? null) && filled($link['url'] ?? null))
            ->unique(fn (array $link): string => $link['label'].'|'.$link['url'])
            ->take(4)
            ->values()
            ->all();
    }
}

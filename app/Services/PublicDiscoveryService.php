<?php

namespace App\Services;

use App\Models\Content;
use App\Models\CourseClass;
use App\Models\Program;
use App\Models\Reel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PublicDiscoveryService
{
    public function __construct(private MediaStorageService $mediaStorage) {}

    /**
     * Ordered from the current canonical profile to supported legacy slugs.
     *
     * @var list<string>
     */
    private const PROFILE_SLUGS = [
        'etc-profile',
        'about',
        'company-profile',
    ];

    public function profile(): ?Content
    {
        $profiles = Content::query()
            ->where('type', Content::TYPE_PROFILE)
            ->whereIn('slug', self::PROFILE_SLUGS)
            ->where('is_published', true)
            ->get()
            ->keyBy('slug');

        foreach (self::PROFILE_SLUGS as $slug) {
            if ($profiles->has($slug)) {
                return $profiles->get($slug);
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Room>
     */
    public function rooms(): Collection
    {
        return Room::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Content>
     */
    public function gallery(): Collection
    {
        return $this->publishedContent('gallery')->get();
    }

    /**
     * @return Collection<int, Content>
     */
    public function partners(): Collection
    {
        return $this->publishedContent('partner')->get();
    }

    /**
     * @return Collection<int, Content>
     */
    public function testimonials(): Collection
    {
        return $this->publishedContent('testimonial')->get();
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
            ->with('activePromotions')
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
        $profile = $this->profile();
        $settings = is_array($profile?->meta) ? $profile->meta : [];

        if ($profile?->body && blank($settings['general_info'] ?? null)) {
            $settings['general_info'] = $profile->body;
        }

        Content::query()
            ->where('type', Content::TYPE_PROFILE)
            ->where('is_published', true)
            ->whereNotIn('slug', self::PROFILE_SLUGS)
            ->orderBy('display_order')
            ->orderBy('title')
            ->get()
            ->each(function (Content $content) use (&$settings): void {
                $key = $content->slug ?: Str::slug($content->title, '_');

                if ($content->slug === 'qris' && $content->image) {
                    $settings[$key] = $this->mediaUrl($content->image);

                    return;
                }

                $hasExplicitValue = is_array($content->meta) && array_key_exists('value', $content->meta);
                $value = $hasExplicitValue ? $content->meta['value'] : $content->body;
                $value ??= $content->image ? $this->mediaUrl($content->image) : null;

                $settings[$key] = $value;
            });

        return collect($settings)
            ->filter(fn (mixed $value): bool => is_array($value) ? $value !== [] : filled($value))
            ->all();
    }

    public function mediaUrl(
        ?string $path,
        string $fallback = 'images/hero-img.jpeg',
        string $resourceType = 'image',
    ): string {
        $url = $this->mediaStorage->url($path, $resourceType);

        return $url ?: asset(ltrim($fallback, '/'));
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    public function faqItems(): array
    {
        return Content::query()
            ->where('type', Content::TYPE_FAQ)
            ->where('is_published', true)
            ->orderBy('display_order')
            ->orderBy('title')
            ->get()
            ->map(fn (Content $content): array => [
                'question' => $content->title,
                'answer' => (string) $content->body,
            ])
            ->all();
    }

    /**
     * @return array<string, int|string|null>
     */
    public function stats(): array
    {
        $testimonialRatings = $this->testimonials()
            ->map(function (Content $testimonial): ?int {
                $rating = $testimonial->meta['rating'] ?? null;

                return is_numeric($rating) ? max(1, min(5, (int) $rating)) : null;
            })
            ->filter(fn (?int $rating): bool => $rating !== null);

        return [
            'students' => User::query()->students()->count(),
            'instructors' => User::query()->instructors()->where('is_active', true)->count(),
            'programs' => Program::query()->where('is_active', true)->count(),
            'satisfaction' => $testimonialRatings->isNotEmpty()
                ? (int) round(($testimonialRatings->average() / 5) * 100).'%'
                : null,
        ];
    }

    /**
     * @return array{intent: string, reply: string}
     */
    public function answerChatbot(string $message): array
    {
        $text = Str::of($message)->lower()->toString();
        $settings = $this->settings();
        $faqAnswer = $this->matchingFaqAnswer($text);

        if ($faqAnswer !== null) {
            return [
                'intent' => 'faq',
                'reply' => $faqAnswer,
            ];
        }

        if (Str::contains($text, ['harga', 'biaya', 'bayar', 'qris', 'transfer'])) {
            return [
                'intent' => 'pricing',
                'reply' => $this->pricingReply(),
            ];
        }

        if (Str::contains($text, ['daftar', 'pendaftaran', 'registrasi', 'join'])) {
            return [
                'intent' => 'registration',
                'reply' => 'Pendaftaran bisa dimulai dari tombol Daftar Sekarang. Pilih program, isi data calon siswa, lalu tim ETC akan membantu tahap pembayaran dan placement test.',
            ];
        }

        if (Str::contains($text, ['jadwal', 'hari', 'jam', 'schedule'])) {
            return [
                'intent' => 'schedule',
                'reply' => $this->scheduleReply(),
            ];
        }

        if (Str::contains($text, ['program', 'kelas', 'toefl', 'ielts', 'english', 'kids', 'teen'])) {
            $programs = $this->programHighlights()->pluck('name')->implode(', ');

            return [
                'intent' => 'program',
                'reply' => $programs !== ''
                    ? 'Program yang sedang tersedia antara lain '.$programs.'. Kamu bisa konsultasi dulu agar level dan target belajarmu lebih pas.'
                    : 'Program ETC Planet belum dipublikasikan. Silakan gunakan form kontak agar tim ETC dapat membantu memilih kelas yang sesuai.',
            ];
        }

        if (Str::contains($text, ['alamat', 'lokasi', 'kontak', 'wa', 'whatsapp', 'instagram'])) {
            $address = $settings['address'] ?? null;
            $contact = $settings['whatsapp'] ?? $settings['phone'] ?? null;
            $instagram = $settings['instagram'] ?? null;

            if (! $address && ! $contact && ! $instagram) {
                return [
                    'intent' => 'contact',
                    'reply' => 'Informasi kontak ETC Planet belum dipublikasikan. Silakan gunakan form kontak agar tim ETC dapat menghubungi kamu.',
                ];
            }

            $parts = array_filter([
                $address ? 'ETC Planet berlokasi di '.$address.'.' : null,
                $contact ? 'Kamu dapat menghubungi '.$contact.' untuk konsultasi.' : null,
                $instagram ? 'Instagram ETC Planet: '.$instagram.'.' : null,
            ]);

            return [
                'intent' => 'contact',
                'reply' => implode(' ', $parts),
            ];
        }

        return [
            'intent' => 'general',
            'reply' => 'Halo! Aku bisa bantu jawab tentang program, biaya, jadwal, pendaftaran, placement test, dan kontak ETC Planet.',
        ];
    }

    protected function publishedContent(string $type): Builder
    {
        return Content::query()
            ->where('type', $type)
            ->where('is_published', true)
            ->orderBy('display_order')
            ->orderBy('title');
    }

    protected function matchingFaqAnswer(string $message): ?string
    {
        $messageWords = $this->searchableWords($message);

        if ($messageWords === []) {
            return null;
        }

        $match = collect($this->faqItems())
            ->map(function (array $faq) use ($messageWords): array {
                $questionWords = $this->searchableWords($faq['question']);

                return [
                    'answer' => $faq['answer'],
                    'score' => count(array_intersect($messageWords, $questionWords)),
                ];
            })
            ->sortByDesc('score')
            ->first();

        return ($match['score'] ?? 0) >= 2 ? $match['answer'] : null;
    }

    protected function pricingReply(): string
    {
        $programs = Program::query()
            ->where('is_active', true)
            ->get(['name', 'price', 'registration_fee']);

        if ($programs->isEmpty()) {
            return 'Informasi biaya program belum dipublikasikan. Silakan gunakan form kontak agar tim ETC dapat memberikan rincian terbaru.';
        }

        $registrationFees = $programs
            ->pluck('registration_fee')
            ->map(fn (mixed $fee): float => (float) $fee)
            ->filter(fn (float $fee): bool => $fee > 0);
        $programPrices = $programs
            ->pluck('price')
            ->map(fn (mixed $price): float => (float) $price)
            ->filter(fn (float $price): bool => $price > 0);
        $parts = [];

        if ($registrationFees->isNotEmpty()) {
            $parts[] = 'Biaya pendaftaran mulai dari '.$this->rupiah((float) $registrationFees->min()).'.';
        }

        if ($programPrices->isNotEmpty()) {
            $minimum = (float) $programPrices->min();
            $maximum = (float) $programPrices->max();
            $parts[] = $minimum === $maximum
                ? 'Biaya program saat ini '.$this->rupiah($minimum).'.'
                : 'Biaya program aktif berkisar '.$this->rupiah($minimum).' sampai '.$this->rupiah($maximum).'.';
        }

        $parts[] = 'Buka halaman Program untuk melihat harga dan promo aktif setiap kelas.';

        return implode(' ', $parts);
    }

    protected function scheduleReply(): string
    {
        $schedules = CourseClass::query()
            ->with('program:id,name')
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where(function (Builder $query): void {
                $query->whereNotNull('schedule_days')
                    ->orWhereNotNull('schedule_time');
            })
            ->orderByRaw("CASE WHEN status = 'ongoing' THEN 0 ELSE 1 END")
            ->orderBy('start_date')
            ->limit(3)
            ->get()
            ->map(function (CourseClass $class): string {
                $schedule = collect([$class->schedule_days, $class->schedule_time])
                    ->filter()
                    ->implode(', ');

                return ($class->program?->name ?? $class->name).': '.$schedule;
            })
            ->filter();

        if ($schedules->isEmpty()) {
            return 'Jadwal kelas aktif belum dipublikasikan. Kamu dapat memilih preferensi hari dan jam saat mendaftar atau menghubungi tim ETC.';
        }

        return 'Jadwal kelas yang tersedia: '.$schedules->implode('; ').'. Jadwal dapat berubah mengikuti kuota kelas.';
    }

    protected function rupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * @return list<string>
     */
    protected function searchableWords(string $value): array
    {
        $stopWords = [
            'adalah',
            'anda',
            'atau',
            'bagaimana',
            'dalam',
            'dengan',
            'dari',
            'ingin',
            'kamu',
            'kami',
            'saya',
            'tentang',
            'untuk',
            'yang',
        ];

        return Str::of($value)
            ->lower()
            ->replaceMatches('/[^\pL\pN]+/u', ' ')
            ->explode(' ')
            ->map(fn (string $word): string => trim($word))
            ->filter(fn (string $word): bool => mb_strlen($word) >= 4 && ! in_array($word, $stopWords, true))
            ->unique()
            ->values()
            ->all();
    }
}

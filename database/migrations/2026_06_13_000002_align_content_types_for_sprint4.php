<?php

use App\Models\Content;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contents')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE contents MODIFY type ENUM('page','gallery','partner','room','team_member_extra','setting','profile','faq','testimonial') NOT NULL");
        }

        Content::query()
            ->where('type', 'setting')
            ->update(['type' => 'profile']);

        Content::query()
            ->where('type', 'team_member_extra')
            ->update(['type' => 'profile']);

        Content::query()
            ->where('type', 'page')
            ->where('slug', 'faq')
            ->get()
            ->each(function (Content $page): void {
                foreach (($page->meta['items'] ?? []) as $index => $item) {
                    if (! is_array($item) || ! isset($item['question'], $item['answer'])) {
                        continue;
                    }

                    Content::query()->updateOrCreate(
                        ['type' => 'faq', 'slug' => 'faq-'.($index + 1)],
                        [
                            'title' => $item['question'],
                            'body' => $item['answer'],
                            'meta' => [],
                            'display_order' => $index + 1,
                            'is_published' => true,
                        ],
                    );
                }
            });

        Content::query()->where('type', 'page')->update(['type' => 'profile']);
        Content::query()->where('type', 'room')->delete();

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE contents MODIFY type ENUM('gallery','partner','profile','faq','testimonial') NOT NULL");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('contents')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE contents MODIFY type ENUM('page','gallery','partner','room','team_member_extra','setting','profile','faq','testimonial') NOT NULL");
        }

        Content::query()->where('type', 'profile')->update(['type' => 'setting']);
        Content::query()->whereIn('type', ['faq', 'testimonial'])->delete();

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE contents MODIFY type ENUM('page','gallery','partner','room','team_member_extra','setting') NOT NULL");
        }
    }
};

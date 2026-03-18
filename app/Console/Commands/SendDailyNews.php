<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RssService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendDailyNews extends Command
{
    // ✅ Added --test option
    protected $signature = 'news:send {--test : Force send anytime}';

    protected $description = 'Send RSS news to Telegram based on schedule';

    public function handle(TelegramService $telegram, RssService $rss)
    {
        $now = Carbon::now();
        $hour = $now->hour;
        $minute = $now->minute;
        $time = $now->format('H:i');

        $forceTest = $this->option('test');

        $this->info("Running at {$time}");
        Log::info("NEWS COMMAND RUNNING at {$time}");

        if ($forceTest) {
            $this->warn("⚠ TEST MODE ENABLED");
        }

        // ======================================
        // 📰 FETCH NEWS
        // ======================================
        $news = $rss->fetchNews();

        // ✅ Safe fallback (unique link)
        if (empty($news)) {
            $news = [
                [
                    'title' => 'Manual Test News',
                    'link' => 'https://manual-test.com/' . now()->timestamp,
                    'date' => now()->toDateTimeString(),
                ]
            ];
        }

        // ======================================
        // 🌅 MORNING DIGEST (9:00 - 9:05)
        // ======================================
        if (($hour === 9 && $minute < 5) && !$forceTest) {

            $this->info("🌅 MORNING DIGEST MODE");

            $queued = DB::table('sent_news')
                ->where('is_sent', false)
                ->get();

            if ($queued->isEmpty()) {
                $this->info("No queued news.");
                return 0;
            }

            foreach ($queued as $item) {
                try {
                    $message = "🌅 <b>MORNING NEWS DIGEST</b>\n\n🔗 {$item->link}";

                    $telegram->sendMessage($message);

                    DB::table('sent_news')
                        ->where('id', $item->id)
                        ->update([
                            'is_sent' => true,
                            'updated_at' => now(),
                        ]);

                    $this->info("Sent morning: {$item->link}");

                } catch (\Exception $e) {
                    Log::error("Morning error: " . $e->getMessage());
                }
            }

            return 0;
        }

        // ======================================
        // ☀ REAL-TIME MODE (9AM–5PM OR TEST)
        // ======================================
        if (($hour >= 9 && $hour < 17) || $forceTest) {

            $this->info("☀ REAL-TIME MODE");

            foreach ($news as $item) {

                $this->line("Processing: " . json_encode($item));

                $link = $item['link'] ?? null;

                if (!$link) {
                    $this->error("❌ Missing link, skipping");
                    continue;
                }

                // ✅ Check duplicate manually
                $exists = DB::table('sent_news')
                    ->where('link', $link)
                    ->exists();

                if ($exists) {
                    $this->warn("⚠ Duplicate: {$link}");
                    continue;
                }

                try {
                    // ✅ INSERT FIRST
                    DB::table('sent_news')->insert([
                        'link' => $link,
                        'is_sent' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->info("✅ Inserted: {$link}");

                    // ✅ THEN SEND
                    $telegram->sendMessage($this->formatMessage($item));

                    $this->info("📤 Sent: " . ($item['title'] ?? 'No title'));

                    Log::info("REALTIME SENT: {$link}");

                } catch (\Exception $e) {
                    $this->error("❌ ERROR: " . $e->getMessage());
                    Log::error("Realtime error: " . $e->getMessage());
                }
            }

            return 0;
        }

        // ======================================
        // 🌙 NIGHT MODE (QUEUE ONLY)
        // ======================================
        $this->info("🌙 NIGHT MODE");

        foreach ($news as $item) {

            $link = $item['link'] ?? null;

            if (!$link) continue;

            $exists = DB::table('sent_news')
                ->where('link', $link)
                ->exists();

            if ($exists) {
                continue;
            }

            try {
                DB::table('sent_news')->insert([
                    'link' => $link,
                    'is_sent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->info("Queued: " . ($item['title'] ?? 'No title'));

                Log::info("QUEUED: {$link}");

            } catch (\Exception $e) {
                Log::error("Queue error: " . $e->getMessage());
            }
        }

        return 0;
    }

    private function formatMessage($item)
    {
        $title = $item['title'] ?? 'No title';
        $link = $item['link'] ?? 'No link';
        $date = $item['date'] ?? now()->toDateTimeString();

        return "🚨 <b>NEWS</b>\n\n<b>{$title}</b>\n\n🔗 {$link}\n🕒 <i>{$date}</i>";
    }
}
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
    protected $signature = 'news:send {--test : Force send anytime} {--realtime : Keep running in console}';
    protected $description = 'Send RSS news to Telegram based on schedule';

    public function handle(TelegramService $telegram, RssService $rss)
    {
        $forceTest = $this->option('test');
        $realtime = $this->option('realtime');

        if ($realtime) {
            $this->info("⚡ REAL-TIME MODE ENABLED (Press Ctrl+C to stop)");
        }

        do {
            $now = Carbon::now('Asia/Manila');
            $hour = $now->hour;
            $minute = $now->minute;
            $time = $now->format('H:i');

            $this->info("Checking news at {$time}");
            Log::info("NEWS COMMAND RUNNING at {$time}");

            // 📰 FETCH NEWS
            $news = $rss->fetchNews();

            // Only consider news published today
            $news = array_filter($news, fn($item) => isset($item['date']) && Carbon::parse($item['date'])->isToday());

            // ✅ TERMINAL FALLBACK if no news today
            if (empty($news)) {
                $fallbackTitle = "No newly published wind/water news today";
                $fallbackDate  = $now->format('Y-m-d H:i:s');

                $this->info("ℹ Fallback: {$fallbackTitle}");
                Log::info("FALLBACK: {$fallbackTitle} at {$fallbackDate}");

                // Skip the rest of the loop
                if (!$realtime) break;
                sleep(60);
                continue;
            }

            foreach ($news as $item) {
                $link = $item['link'] ?? null;
                $title = $item['title'] ?? 'No title';
                $pubDate = isset($item['date']) ? Carbon::parse($item['date'])->setTimezone('Asia/Manila') : $now;

                if (!$link) continue;

                // Skip duplicates
                if (DB::table('sent_news')->where('link', $link)->exists()) {
                    $this->line("⚠ Duplicate skipped: {$title}");
                    continue;
                }

                $sendNow = false;

                // Determine sending status
                if ($forceTest) {
                    $sendNow = true;
                } else {
                    if ($pubDate->isToday() && $hour >= 9 && $hour < 17) {
                        $sendNow = true;
                    }
                }

                try {
                    if ($sendNow) {
                        // Send immediately
                        DB::table('sent_news')->insert([
                            'link' => $link,
                            'is_sent' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $telegram->sendMessage($this->formatMessage($item));
                        $this->info("📤 Sent: {$title}");
                        Log::info("REALTIME SENT: {$link}");
                    } else {
                        // Queue for morning digest
                        DB::table('sent_news')->insert([
                            'link' => $link,
                            'is_sent' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $this->info("🌙 Queued: {$title}");
                        Log::info("QUEUED: {$link}");
                    }
                } catch (\Exception $e) {
                    $this->error("❌ Failed: {$title} ({$link})");
                    Log::error("Error processing {$link}: " . $e->getMessage());
                }
            }

            // 🌅 MORNING DIGEST (9:00 - 9:05)
            if ($hour === 9 && $minute < 5) {
                $queued = DB::table('sent_news')->where('is_sent', false)->get();
                foreach ($queued as $item) {
                    try {
                        $telegram->sendMessage("🌅 <b>MORNING NEWS DIGEST</b>\n\n🔗 {$item->link}");
                        DB::table('sent_news')->where('id', $item->id)->update([
                            'is_sent' => true,
                            'updated_at' => now(),
                        ]);
                        $this->info("Sent morning: {$item->link}");
                        Log::info("MORNING DIGEST SENT: {$item->link}");
                    } catch (\Exception $e) {
                        Log::error("Morning send failed: " . $e->getMessage());
                    }
                }
            }

            if (!$realtime) break;

            sleep(60);

        } while ($realtime);

        return 0;
    }

    private function formatMessage($item)
    {
        $title = $item['title'] ?? 'No title';
        $link = $item['link'] ?? 'No link';
        $date = $item['date'] ?? now()->toDateTimeString();

        return "🚨 <b>WIND & WATER NEWS</b>\n\n<b>{$title}</b>\n\n🔗 {$link}\n🕒 <i>{$date}</i>";
    }
}
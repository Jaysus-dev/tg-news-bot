<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RssService;
use Carbon\Carbon;

class RssDebug extends Command
{
    protected $signature = 'rss:debug';
    protected $description = 'Debug RSS feeds with 9AM–5PM send / queue logic';

    public function handle()
    {
        $rss = new RssService();
        $allNews = $rss->fetchNews(); // fetch all news matching keywords

        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString(); // YYYY-MM-DD
        $currentHour = $now->hour;

        $this->info("🕒 Current Time: " . $now->format('Y-m-d H:i:s'));
        $this->line(str_repeat('=', 60));

        // ✅ Filter only today's news
        $news = array_filter($allNews, function ($item) use ($today) {
            $pubDate = Carbon::parse($item['date'])->setTimezone('Asia/Manila');
            return $pubDate->toDateString() === $today;
        });

        if (empty($news)) {
            $this->warn("⚠ No news published today matching keywords.");
            return;
        }

        $sendCount = 0;
        $queueCount = 0;

        foreach ($news as $item) {
            $title = $item['title'];
            $link  = $item['link'];
            $date  = Carbon::parse($item['date'])->setTimezone('Asia/Manila');

            // 🔥 Determine action based on current time
            if ($currentHour >= 9 && $currentHour < 17) {
                $status = '🟢 SEND NOW';
                $sendCount++;
            } else {
                $status = '🌙 QUEUE (Morning Digest)';
                $queueCount++;

                // Early morning special label before 9AM
                if ($currentHour < 9) {
                    $status = '🌅 WILL SEND AT 9AM';
                }
            }

            // Display the news item with status
            $this->line("📰 Title : {$title}");
            $this->line("🔗 Link  : {$link}");
            $this->line("🕒 Date  : {$date->format('Y-m-d H:i')}");
            $this->line("📌 Status: {$status}");
            $this->line(str_repeat('-', 60));
        }

        // 📊 Summary of send vs queued
        $this->info("📊 SUMMARY:");
        $this->line("🟢 To Send Now: {$sendCount}");
        $this->line("🌙 Queued     : {$queueCount}");
    }
}
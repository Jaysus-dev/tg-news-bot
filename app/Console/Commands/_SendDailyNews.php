<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RssService;
use App\Services\TelegramService;

class _SendDailyNews extends Command
{
    protected $signature = 'news:send';
    protected $description = 'Send RSS news one by one to Telegram';

    public function handle(TelegramService $telegram, RssService $rss)
    {
        $news = $rss->fetchNews(); // fetch all matching news

        if (empty($news)) {
            $this->info("No Related news to send today.");
            return 0;
        }

        foreach ($news as $item) {
            $message = "<b>" . $item['title'] . "</b>\n";
            $message .= $item['link'] . "\n";
            $message .= "<i>" . $item['date'] . "</i>";

            $telegram->sendMessage($message);
        
            $this->info("Sent: " . $item['title']);

            // Optional delay to avoid Telegram limits
            sleep(2);
        }

        $this->info("All news sent successfully.");
        return 0;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RssService;

class RssDebug extends Command
{
    protected $signature = 'rss:debug';
    protected $description = 'Debug RSS feeds';

    public function handle()
    {
        $rss = new RssService();
        $news = $rss->fetchNews();

        if (empty($news)) {
            $this->info("No news matched keywords.");
        } else {
            $this->info("News matching keywords:");
            foreach ($news as $item) {
                $this->line("Title: " . $item['title']);
                $this->line("Link : " . $item['link']);
                $this->line("Date : " . $item['date']);
                $this->line(str_repeat('-', 50));
            }
        }
    }
}
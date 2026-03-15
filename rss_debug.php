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
        $news = $rss->fetchNews(5);

        if (empty($news)) {
            $this->warn("No news matched your keywords.");
            return;
        }

        foreach ($news as $item) {
            $this->line($item['title']);
            $this->line($item['link']);
            $this->line($item['date']);
            $this->line(str_repeat("-", 50));
        }
    }
}
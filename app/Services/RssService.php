<?php

namespace App\Services;

use Feeds; // from willvincent/feeds

class RssService
{
    protected $feeds = [
        // Mainstream news
        'https://news.abs-cbn.com/rss',
        'https://www.gmanetwork.com/news/rss/news',
        'https://www.inquirer.net/fullfeed',
        'interaksyon.philstar.com/feed',
        'mindanaotimes.com.ph/feed',
        'mindanews.com/feed',
        'davaotoday.com/feed',
        'visayandailystar.com/feed',
        'punto.com.ph/feed',
        'boholchronicle.com.ph/feed',
        'punch.dagupan.com/feed',
        'subicbaynews.com/feed',
        'https://tempo.mb.com.ph/feed/',
        'tonite.abante.com.ph/feed',
        'philnews.ph/feed',
        'https://feeds.feedburner.com/TheSummitExpress',
        'https://pilipinasdaily.com/feed/',
        'https://www.rappler.com/environment/feed/',
       

    ];
protected $keywords = [
    'wind',
    'energy',
    'renewable',
    'power',
    'water',
    'flood',
    'rain',
    'dam',
    'reservoir',
    'irrigation',
    'water supply',
    'water interruption'
];

    public function fetchNews()
    {
        $results = [];

        foreach ($this->feeds as $feedUrl) {
            try {
                $feed = Feeds::make($feedUrl, 5); // fetch 5 items max per feed
                $items = $feed->get_items();

                foreach ($items as $item) {
                    $title = strtolower($item->get_title());

                    foreach ($this->keywords as $keyword) {
                        if (str_contains($title, $keyword)) {
                            $results[] = [
                                'title' => $item->get_title(),
                                'link' => $item->get_link(),
                                'date' => $item->get_date('Y-m-d H:i:s'),
                            ];
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue; // skip invalid feeds
            }
        }

        return $results;
    }
}
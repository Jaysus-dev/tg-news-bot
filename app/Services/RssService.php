<?php

namespace App\Services;

use Feeds;
use Carbon\Carbon;

class RssService
{
    protected $feeds = [
        'https://news.abs-cbn.com/rss',
        'https://www.gmanetwork.com/news/rss/news',
        'https://www.inquirer.net/fullfeed',
        'https://www.philstar.com/rss/headlines',
        'https://www.philstar.com/rss/nation',
        'https://balita.mb.com.ph/rssfeed/0/',
        'https://balita.mb.com.ph/rssFeed/14/',
        'https://interaksyon.philstar.com/feed',
        'https://mindanaotimes.com.ph/feed',
        'https://mindanews.com/feed',
        'https://davaotoday.com/feed',
        'https://visayandailystar.com/feed',
        'https://punto.com.ph/feed',
        'https://boholchronicle.com.ph/feed',
        'https://punch.dagupan.com/feed',
        'https://subicbaynews.com/feed',
        'https://tempo.mb.com.ph/feed/',
        'https://tonite.abante.com.ph/feed',
        'https://philnews.ph/feed',
        'https://feeds.feedburner.com/TheSummitExpress',
        'https://pilipinasdaily.com/feed/',
        'https://www.rappler.com/environment/feed/',
    ];

   protected $keywords = [
        // Wind / Renewable energy
        'wind projects',
         'wind project',
        'wind energy',
        'renewable energy',
        'solar',
        'solar power',
        'hydro',
        'hydropower',

        // Water District / Water supply / Water interruptions
        'water district',
        'water interruption',
        'water supply',
        'flood',
        'reservoir',
        'irrigation',
        'drinking water',
        'hydration',
    ];

    public function fetchNews($maxPerFeed = 20)
    {
        $results = [];

        foreach ($this->feeds as $feedUrl) {
            try {
                $feed = Feeds::make($feedUrl, $maxPerFeed);
                $items = $feed->get_items();

                foreach ($items as $item) {
                    $title = strtolower($item->get_title());

                    foreach ($this->keywords as $keyword) {
                        if (str_contains($title, $keyword)) {
                            $results[] = [
                                'title' => $item->get_title(),
                                'link'  => $item->get_link(),
                                'date'  => Carbon::parse($item->get_date())->format('Y-m-d H:i:s'),
                            ];
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Fallback test news if empty
        if (empty($results)) {
            $results[] = [
                'title' => 'Manual Test News',
                'link' => 'https://manual-test.com',
                'date' => now()->toDateTimeString(),
            ];
        }

        return $results;
    }
}
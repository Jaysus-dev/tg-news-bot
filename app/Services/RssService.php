<?php

namespace App\Services;

use Feeds;
use Carbon\Carbon;

class RssService
{
    protected $feeds = [
        // Mainstream news
        'https://news.abs-cbn.com/rss',
        'https://www.gmanetwork.com/news/rss/news',
        'https://www.inquirer.net/fullfeed',
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

    /**
     * Fetch news from all feeds filtered by keywords and date window.
     *
     * @param int $maxPerFeed
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function fetchNews($maxPerFeed = 10, $startDate = null, $endDate = null)
    {
        $results = [];

        // Daily window: 9:00 AM previous day → 8:59 AM today
        $startDate ??= Carbon::now()->subDay()->setHour(9)->setMinute(0)->setSecond(0);
        $endDate ??= Carbon::now()->setHour(8)->setMinute(59)->setSecond(59);

        foreach ($this->feeds as $feedUrl) {
            try {
                $feed = Feeds::make($feedUrl, $maxPerFeed);
                $items = $feed->get_items();

                foreach ($items as $item) {
                    $title = strtolower($item->get_title());
                    $pubDate = Carbon::parse($item->get_date());

                    // Check if date is within window
                    if ($pubDate->lt($startDate) || $pubDate->gt($endDate)) {
                        // Uncomment to debug skipped articles by date
                        // echo "[SKIP - DATE] " . $item->get_title() . " | " . $pubDate . "\n";
                        continue;
                    }

                    // Check for keywords
                    foreach ($this->keywords as $keyword) {
                        if (str_contains($title, $keyword)) {
                            $results[] = [
                                'title' => $item->get_title(),
                                'link'  => $item->get_link(),
                                'date'  => $pubDate->format('Y-m-d H:i:s'),
                            ];
                            break; // matched one keyword, skip others
                        }
                    }
                }

            } catch (\Exception $e) {
                // Uncomment to debug failed feeds
                // echo "Failed feed: $feedUrl | " . $e->getMessage() . "\n";
                continue;
            }
        }

        return $results;
    }
}
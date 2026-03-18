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
        'https://cebudailynews.inquirer.net/feed',
        'https://www.bworldonline.com/feed/',
        'https://www.manilatimes.net/news/feed/',
        'https://www.manilatimes.net/news/national/feed/',
        'https://www.manilatimes.net/regions/feed/',
        'https://punto.com.ph/feed/',
        'https://visayandailystar.com/feed',
        'https://philnews.ph/feed/',
        'https://metrocebu.news/feed/',
        'https://ourdailynewsonline.com/feed/',
        'https://currentph.com/feed/',
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
    // 🌬 Wind / Renewable Energy
    'wind projects',
    'wind project',
    'wind energy',
    'wind turbine',
    'wind turbines',
    'wind farm',
    'wind farms',
    'offshore wind',
    'onshore wind',
    'sustainable energy',
    'green energy',
    'renewable energy',
    'energy transition',
    'solar power',
    'solar energy',
    'solar project',
      'hydropower plant',
    'hydropower',
    'hydroelectric project',
    'geothermal energy',
    'clean energy',
    'manila electric company',
    'energy infrastructure',
    'power generation',
    'energy capacity',
    'energy development',
    'energy sector',
    'renewable project',

    // 💧 Water District / Water Supply / Water Interruptions
    'water district',
    'water districts',
    'water interruption',
    'water outage',
    'water supply',
    'water service updates',
    'water quality',
    'reservoir',
    'reservoir levels',
    'water treatment',
    'distribution schedule',
    'utility notice',
    'hydration alert',
    'water infrastructure',
    'water management',
    'drinking water',
    'water crisis',
    'water conservation',
    'flood updates',
    'drought conditions',
    'groundwater levels',
    'river pollution',
    'water policy',
    'hydrology research',
    'irrigation project',
    'water safety',
    'public water supply',
    'manila water',
    'maynilad',
    'metro manila water',
    'provincial water updates',
    'water scarcity',
    'water shortage',
    'water pricing',
    'water tariff',
    'water advisory',
    'service interruption',
    'pipe leak',
    'water distribution',
    'water monitoring',
    'dam levels',
    'water resources',
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
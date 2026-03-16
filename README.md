Telegram News Bot

Telegram News Bot is an automated news aggregation bot that collects updates from multiple Philippine news sources and water-related organizations, then posts relevant updates to a Telegram group chat. It ensures group members receive timely information without manually checking multiple websites.

Features

Automatic News Fetching: Collects articles from multiple RSS feeds and sources.

Real-time Updates: Sends news updates directly to a Telegram group chat.

Scheduled Monitoring: Automatically checks feeds at regular intervals and posts new articles.

Topic Filtering: Filters news related to:

Wind Projects

Water District Updates

Water-related news

Multiple News Sources: Integrates with major Philippine news outlets and government-related websites.

News Sources

The bot collects news from various Philippine sources including, but not limited to:

DOE updates

LWUA news

DENR announcements

Water district websites

ABS-CBN News

GMA News

Manila Bulletin

Inquirer.net

Rappler

Sources are fetched via RSS feeds, then filtered according to topics.

Architecture
News Sources (RSS Feeds)
        │
        ▼
   Laravel RSS Service
        │
        ▼
  News Filtering (Wind / Water Topics)
        │
        ▼
   Telegram Bot API
        │
        ▼
   Telegram Group Chat

Laravel RSS Service: Handles feed fetching and parsing.

News Filtering: Extracts relevant articles based on keywords/topics.

Telegram Bot API: Sends messages to your group automatically.

News Fetching Window

The bot fetches and sends news published within the daily window:

Start: 9:00 AM (previous day)

End: 8:59 AM (current day)

Example:
If today is March 16:

The bot fetches articles from March 15, 9:00 AM to March 16, 8:59 AM.

Messages are sent to the Telegram group once the scheduler runs.

This ensures all news for the day is collected and sent once daily, avoiding duplicates.

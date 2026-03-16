 <h1>Telegram News Bot</h1>
  <p>
    <strong>Telegram News Bot</strong> is an automated news aggregation bot that collects updates from multiple Philippine news sources and water-related organizations, then posts relevant updates to a Telegram group chat. It ensures group members receive timely information without manually checking multiple websites.
  </p>

  <h2>Features</h2>
  <ul>
    <li><strong>Automatic News Fetching:</strong> Collects articles from multiple RSS feeds and sources.</li>
    <li><strong>Real-time Updates:</strong> Sends news updates directly to a Telegram group chat.</li>
    <li><strong>Scheduled Monitoring:</strong> Automatically checks feeds at regular intervals and posts new articles.</li>
    <li><strong>Topic Filtering:</strong> Filters news related to:
      <ul>
        <li>Wind Projects</li>
        <li>Water District Updates</li>
        <li>Water-related news</li>
      </ul>
    </li>
    <li><strong>Multiple News Sources:</strong> Integrates with major Philippine news outlets and government-related websites.</li>
  </ul>

  <h2>News Sources</h2>
  <ul>
    <li>DOE updates</li>
    <li>LWUA news</li>
    <li>DENR announcements</li>
    <li>Water district websites</li>
    <li>ABS-CBN News</li>
    <li>GMA News</li>
    <li>Manila Bulletin</li>
    <li>Inquirer.net</li>
    <li>Rappler</li>
  </ul>
  <blockquote>
    Sources are fetched via <strong>RSS feeds</strong>, then filtered according to topics.
  </blockquote>

  <h2>Architecture</h2>
  <pre>
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
  </pre>
  <p>
    <strong>Laravel RSS Service:</strong> Handles feed fetching and parsing.<br>
    <strong>News Filtering:</strong> Extracts relevant articles based on keywords/topics.<br>
    <strong>Telegram Bot API:</strong> Sends messages to your group automatically.
  </p>

  <h2>News Fetching Window</h2>
  <p>The bot fetches and sends news published within the <strong>daily window</strong>:</p>
  <ul>
    <li><strong>Start:</strong> 9:00 AM (previous day)</li>
    <li><strong>End:</strong> 8:59 AM (current day)</li>
  </ul>
  <p><strong>Example:</strong> If today is <em>March 16</em>:</p>
  <ul>
    <li>The bot fetches articles from <strong>March 15, 9:00 AM</strong> to <strong>March 16, 8:59 AM</strong>.</li>
    <li>Messages are sent to the Telegram group once the scheduler runs.</li>
  </ul>
  <p>This ensures all news for the day is collected and sent once daily, avoiding duplicates.</p>

  <h2>Installation</h2>
  <ol>
    <li>
      <strong>Clone the repository</strong>
      <pre>git clone https://github.com/Jaysus-dev/telegram-news-bot.git
cd telegram-news-bot</pre>
    </li>
    <li>
      <strong>Install dependencies</strong>
      <pre>composer install</pre>
    </li>
    <li>
      <strong>Setup environment</strong>
      <pre>cp .env.example .env
php artisan key:generate</pre>
      <p>Configure the following in <code>.env</code>:</p>
      <pre>
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_group_chat_id_here
      </pre>
    </li>
    <li>
      <strong>Run migrations (if needed)</strong>
      <pre>php artisan migrate</pre>
    </li>
  </ol>

  <h2>Usage</h2>
  <h3>Manual Run</h3>
  <pre>php artisan telegram:send-news</pre>
  <p>This fetches news and sends updates immediately.</p>

  <h3>Scheduled Run</h3>
  <p>Add to your Laravel scheduler (<code>app/Console/Kernel.php</code>):</p>
  <pre>$schedule->command('telegram:send-news')->dailyAt('09:00');</pre>
  <p><em>Ensure your server or hosting provider runs Laravel’s scheduler (<code>php artisan schedule:run</code>) every minute.</em></p>

  <h2>Contributing</h2>
  <ol>
    <li>Fork the repository</li>
    <li>Create your feature branch (<code>git checkout -b feature/new-feature</code>)</li>
    <li>Commit your changes (<code>git commit -m 'Add new feature'</code>)</li>
    <li>Push to the branch (<code>git push origin feature/new-feature</code>)</li>
    <li>Open a pull request</li>
  </ol>

  <h2>Author</h2>
  <p><strong>Jaysus-dev</strong> – Developer & Maintainer<br>
  <a href="https://github.com/Jaysus-dev">GitHub Profile</a></p>

</body>
</html>

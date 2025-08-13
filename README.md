# Discord X-Cancel Bot

A Discord bot that automatically converts Twitter and X.com links to alternative domains (like xcancel.com) for better privacy and functionality. Built with [Laracord](https://laracord.com), a Laravel-based Discord bot framework.

## Features

- 🔄 **Automatic Link Conversion**: Automatically detects and converts Twitter/X.com links in messages
- ⚙️ **Per-Guild Configuration**: Server administrators can configure bot settings per Discord server
- 📊 **Statistics Tracking**: Tracks conversion statistics (Twitter vs X.com links)
- 🎯 **Slash Commands**: Easy-to-use slash commands for manual conversion and configuration
- 🛡️ **Admin Controls**: Admin-only configuration commands with proper permission checks
- 🗄️ **Database Storage**: Persistent storage of guild settings and user data

## Commands

### `/convert`

Manually convert Twitter/X links in a message.

- **Usage**: `/convert message:"Your message with Twitter links"`
- **Available to**: All users

### `/xcancel-config`

Configure bot settings for your server (Admin only).

- **Usage**: `/xcancel-config <action> [value]`
- **Actions**:
  - `show` - Display current settings
  - `enable` - Enable automatic link conversion
  - `disable` - Disable automatic link conversion
  - `set-domain` - Set custom conversion domain
  - `stats` - View conversion statistics
- **Available to**: Server administrators only

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- A Discord bot token
- SQLite (default) or other database

### Setup

1. **Clone the repository**:

   ```bash
   git clone <repository-url>
   cd discord-x-cancel
   ```

2. **Install dependencies**:

   ```bash
   composer install
   ```

3. **Configure environment**:

   ```bash
   cp .env.example .env
   ```

   Edit `.env` and add your Discord bot token:

   ```env
   DISCORD_TOKEN=your_bot_token_here
   XCANCEL_DOMAIN=xcancel.com
   ```

4. **Run database migrations**:

   ```bash
   php laracord migrate
   ```

5. **Start the bot**:
   ```bash
   php laracord bot
   ```

## Configuration

### Environment Variables

- `DISCORD_TOKEN`: Your Discord bot token (required)
- `XCANCEL_DOMAIN`: Default domain for link conversion (default: `xcancel.com`)

### Discord Bot Setup

1. Go to the [Discord Developer Portal](https://discord.com/developers/applications)
2. Create a new application and bot
3. Copy the bot token to your `.env` file
4. Enable the following bot permissions:
   - Send Messages
   - Use Slash Commands
   - Read Message History
   - Embed Links

### Invite the Bot

Generate an invite link with the following permissions:

- `applications.commands` (Slash Commands)
- `bot` with permissions: Send Messages, Use Slash Commands, Read Message History, Embed Links

## How It Works

1. **Automatic Conversion**: The bot listens to all messages in channels where it has access
2. **Link Detection**: Uses regex patterns to detect Twitter and X.com links
3. **Conversion**: Replaces detected links with the configured alternative domain
4. **Response**: Posts the converted message with an embed showing statistics

### Supported Link Patterns

- `twitter.com/*` → `{domain}/*`
- `x.com/*` → `{domain}/*`
- Supports both HTTP and HTTPS
- Handles www. subdomains

## Database Schema

The bot uses the following database tables:

- **users**: Stores Discord user information
- **guild_settings**: Stores per-server configuration
  - `guild_id`: Discord server ID
  - `auto_convert`: Enable/disable automatic conversion
  - `domain`: Custom conversion domain
  - `stats`: JSON field for tracking statistics

## Development

### File Structure

```
app/
├── Bot.php                     # Main bot class
├── Commands/
│   └── PingCommand.php         # Basic ping command
├── Events/
│   └── MessageCreate.php       # Handles incoming messages
├── Models/
│   ├── GuildSetting.php        # Guild configuration model
│   └── User.php                # User model
├── Services/
│   └── LinkConversionService.php # Core link conversion logic
├── SlashCommands/
│   ├── ConfigSlashCommand.php   # Configuration commands
│   └── ConvertSlashCommand.php  # Manual conversion command
└── Traits/
    └── HasAdminCheck.php        # Admin permission trait
```

### Adding New Link Patterns

Edit `config/xcancel.php` to add new conversion patterns:

```php
'patterns' => [
    'twitter' => [
        'match' => '/https?:\/\/(?:www\.)?twitter\.com\/(\S+)/',
        'replace' => 'https://{domain}/$1',
    ],
    'x' => [
        'match' => '/https?:\/\/(?:www\.)?x\.com\/(\S+)/',
        'replace' => 'https://{domain}/$1',
    ],
    // Add new patterns here
],
```

### Running Tests

```bash
php artisan test
```

### Code Style

Format code using Laravel Pint:

```bash
./vendor/bin/pint
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and linting
5. Submit a pull request

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Built With

- [Laracord](https://laracord.com) - Laravel-based Discord bot framework
- [DiscordPHP](https://github.com/discord-php/DiscordPHP) - Discord API wrapper
- [Laravel Zero](https://laravel-zero.com) - Micro-framework for console applications

## Support

If you encounter any issues or have questions:

1. Check the [Laracord documentation](https://laracord.com/docs)
2. Open an issue on GitHub
3. Join the Discord community

---

_Made with ❤️ using Laracord_

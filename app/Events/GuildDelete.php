<?php

namespace App\Events;

use App\Models\GuildSetting;
use Discord\Discord;
use Discord\WebSockets\Event as Events;
use Laracord\Events\Event;

class GuildDelete extends Event
{
    protected $handler = Events::GUILD_DELETE;

    public function handle(object $guild, Discord $discord, bool $unavailable)
    {
        // If guild is unavailable due to an outage, don't delete data
        if ($unavailable) {
            return;
        }

        // The bot has been removed from the guild - clean up data
        $guildId = is_object($guild) && isset($guild->id) ? $guild->id : $guild->guild_id ?? null;
        
        if ($guildId) {
            // Delete guild settings and associated data
            $deleted = GuildSetting::where('guild_id', $guildId)->delete();
            
            if ($deleted) {
                $this->info("Cleaned up data for guild {$guildId} (bot was removed from server)");
            }
        }
    }
}
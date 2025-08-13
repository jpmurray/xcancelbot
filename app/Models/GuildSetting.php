<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuildSetting extends Model
{
    protected $fillable = [
        'guild_id',
        'enabled',
        'show_credit',
        'auto_mode',
        'twitter_conversions',
        'x_conversions',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'show_credit' => 'boolean',
        'auto_mode' => 'boolean',
        'twitter_conversions' => 'integer',
        'x_conversions' => 'integer',
    ];

    /**
     * Get settings for a guild, creating defaults if not exists
     */
    public static function getForGuild(string $guildId): self
    {
        return static::firstOrCreate(
            ['guild_id' => $guildId],
            [
                'enabled' => true,
                'show_credit' => true,
                'auto_mode' => true,
                'twitter_conversions' => 0,
                'x_conversions' => 0,
            ]
        );
    }

    /**
     * Update setting for a guild
     */
    public static function updateForGuild(string $guildId, string $key, $value): void
    {
        static::updateOrCreate(
            ['guild_id' => $guildId],
            [$key => $value]
        );
    }

    /**
     * Increment conversion statistics for a guild
     */
    public static function incrementStats(string $guildId, array $stats): void
    {
        $setting = static::firstOrCreate(['guild_id' => $guildId]);
        
        if (isset($stats['twitter_conversions'])) {
            $setting->increment('twitter_conversions', $stats['twitter_conversions']);
        }
        
        if (isset($stats['x_conversions'])) {
            $setting->increment('x_conversions', $stats['x_conversions']);
        }
    }
}

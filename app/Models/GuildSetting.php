<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuildSetting extends Model
{
    protected $fillable = [
        'guild_id',
        'guild_name',
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
    public static function getForGuild(string $guildId, ?string $guildName = null): self
    {
        $setting = static::where('guild_id', $guildId)->first();
        
        // If setting doesn't exist, create it
        if (!$setting) {
            $attributes = [
                'guild_id' => $guildId,
                'enabled' => true,
                'show_credit' => true,
                'auto_mode' => true,
                'twitter_conversions' => 0,
                'x_conversions' => 0,
            ];
            
            if ($guildName) {
                $attributes['guild_name'] = $guildName;
            }
            
            $setting = static::create($attributes);
        } else {
            // Only update guild name if provided and different
            if ($guildName && $setting->guild_name !== $guildName) {
                $setting->update(['guild_name' => $guildName]);
            }
        }
        
        return $setting;
    }

    /**
     * Update setting for a guild
     */
    public static function updateForGuild(string $guildId, string $key, $value, ?string $guildName = null): void
    {
        $setting = static::where('guild_id', $guildId)->first();
        
        if (!$setting) {
            // Create new setting if it doesn't exist
            $attributes = [
                'guild_id' => $guildId,
                $key => $value,
                'enabled' => true,
                'show_credit' => true,
                'auto_mode' => true,
                'twitter_conversions' => 0,
                'x_conversions' => 0,
            ];
            
            if ($guildName) {
                $attributes['guild_name'] = $guildName;
            }
            
            static::create($attributes);
        } else {
            // Only update if values are different
            $updates = [];
            
            if ($setting->$key !== $value) {
                $updates[$key] = $value;
            }
            
            if ($guildName && $setting->guild_name !== $guildName) {
                $updates['guild_name'] = $guildName;
            }
            
            if (!empty($updates)) {
                $setting->update($updates);
            }
        }
    }

    /**
     * Increment conversion statistics for a guild
     */
    public static function incrementStats(string $guildId, array $stats, ?string $guildName = null): void
    {
        $defaults = [];
        if ($guildName) {
            $defaults['guild_name'] = $guildName;
        }
        
        $setting = static::firstOrCreate(['guild_id' => $guildId], $defaults);
        
        // Update guild name if provided and different
        if ($guildName && $setting->guild_name !== $guildName) {
            $setting->update(['guild_name' => $guildName]);
        }
        
        if (isset($stats['twitter_conversions'])) {
            $setting->increment('twitter_conversions', $stats['twitter_conversions']);
        }
        
        if (isset($stats['x_conversions'])) {
            $setting->increment('x_conversions', $stats['x_conversions']);
        }
    }
}

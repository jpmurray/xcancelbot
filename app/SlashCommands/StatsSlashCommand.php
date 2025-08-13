<?php

namespace App\SlashCommands;

use App\Models\GuildSetting;
use Discord\Parts\Interactions\Interaction;
use Laracord\Commands\SlashCommand;

class StatsSlashCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'xcancel-stats';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Display server statistics for X-Cancel conversions';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The permissions required to use the command.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Indicates whether the command requires admin permissions.
     *
     * @var bool
     */
    protected $admin = false;

    /**
     * Indicates whether the command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Handle the slash command.
     *
     * @param  \Discord\Parts\Interactions\Interaction  $interaction
     * @return mixed
     */
    public function handle($interaction)
    {
        $guildId = $interaction->guild_id;
        
        if (!$guildId) {
            $interaction->respondWithMessage(
                $this->message('❌ This command can only be used in a server.')->build(),
                true // ephemeral - only visible to the user
            );
            return;
        }
        
        $settings = GuildSetting::getForGuild($guildId);
        $totalConversions = $settings->twitter_conversions + $settings->x_conversions;
        
        $interaction->respondWithMessage(
            $this
                ->message('Server conversion statistics')
                ->title('📊 X-Cancel Server Statistics')
                ->field('Twitter Links Converted', number_format($settings->twitter_conversions), true)
                ->field('X.com Links Converted', number_format($settings->x_conversions), true)
                ->field('Total Conversions', number_format($totalConversions), true)
                ->field('Bot Status', $settings->enabled ? '✅ Enabled' : '❌ Disabled', true)
                ->field('Auto Mode', $settings->auto_mode ? '🤖 Automatic' : '⚡ Manual', true)
                ->field('Show Credits', $settings->show_credit ? '✅ Yes' : '❌ No', true)
                ->build()
        );
    }

    /**
     * The command interaction routes.
     */
    public function interactions(): array
    {
        return [];
    }
}
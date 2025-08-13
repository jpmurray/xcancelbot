<?php

namespace App\SlashCommands;

use App\Models\GuildSetting;
use App\Traits\HasAdminCheck;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Laracord\Commands\SlashCommand;

class ConfigSlashCommand extends SlashCommand
{
    use HasAdminCheck;
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'xcancel-config';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Configure X-Cancel bot settings';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [
        [
            'name' => 'setting',
            'description' => 'The setting to configure',
            'type' => Option::STRING,
            'required' => false,
            'choices' => [
                ['name' => 'enabled', 'value' => 'enabled'],
                ['name' => 'credit', 'value' => 'credit'],
                ['name' => 'auto', 'value' => 'auto'],
            ]
        ],
        [
            'name' => 'value',
            'description' => 'The value to set (true/false)',
            'type' => Option::BOOLEAN,
            'required' => false,
        ]
    ];

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
        // Check if user has admin permissions (server owner or configured admin)
        if (!$this->isUserAdmin($interaction)) {
            $interaction->respondWithMessage(
                $this->message('❌ You need admin permissions to use this command.')->build(),
                true // ephemeral - only visible to the user
            );
            return;
        }
        
        $guildId = $interaction->guild_id;
        
        $setting = $this->value('setting');
        $value = $this->value('value');
        
        // If no setting provided, show current settings
        if (!$setting) {
            return $this->showCurrentSettings($interaction, $guildId);
        }
        
        // If setting provided but no value, show help for that setting
        if ($value === null) {
            return $this->showSettingHelp($interaction, $setting);
        }
        
        // Update the setting
        $this->updateSetting($interaction, $guildId, $setting, $value);
    }

    /**
     * Show current settings
     */
    private function showCurrentSettings($interaction, $guildId)
    {
        $settings = GuildSetting::getForGuild($guildId);
        
        $interaction->respondWithMessage(
            $this
                ->message('Current X-Cancel bot settings:')
                ->title('🔧 Bot Configuration')
                ->field('Enabled', $settings->enabled ? '✅ Yes' : '❌ No', true)
                ->field('Show Credit', $settings->show_credit ? '✅ Yes' : '❌ No', true)
                ->field('Auto Mode', $settings->auto_mode ? '✅ Automatic' : '❌ Manual (!xcancel)', true)
                ->field('Embeds', '✅ Always Enabled', true)
                ->field('📊 Statistics', '', false)
                ->field('Twitter Links', $settings->twitter_conversions . ' converted', true)
                ->field('X.com Links', $settings->x_conversions . ' converted', true)
                ->field('Total', ($settings->twitter_conversions + $settings->x_conversions) . ' conversions', true)
                ->field('Usage', 'Use `/xcancel-config <setting> <value>` to change settings')
                ->build()
        );
    }

    /**
     * Show help for a specific setting
     */
    private function showSettingHelp($interaction, $setting)
    {
        $help = match($setting) {
            'enabled' => 'Enable or disable the X-Cancel bot for this server',
            'credit' => 'Show or hide user credit in bot responses',
            'auto' => 'Toggle between automatic conversion and manual (!convert command)',
            default => 'Unknown setting'
        };
        
        $interaction->respondWithMessage(
            $this
                ->message($help)
                ->title("🔧 Setting: {$setting}")
                ->field('Usage', "/xcancel-config {$setting} true/false")
                ->build()
        );
    }

    /**
     * Update a setting
     */
    private function updateSetting($interaction, $guildId, $setting, $value)
    {
        $dbKey = match($setting) {
            'enabled' => 'enabled',
            'credit' => 'show_credit',
            'auto' => 'auto_mode',
            default => null
        };
        
        if (!$dbKey) {
            $interaction->respondWithMessage(
                $this->message('❌ Invalid setting provided.')->build(),
                true // ephemeral - only visible to the user
            );
            return;
        }
        
        GuildSetting::updateForGuild($guildId, $dbKey, $value);
        
        $displayValue = $value ? '✅ Enabled' : '❌ Disabled';
        $settingName = ucfirst($setting);
        
        $interaction->respondWithMessage(
            $this
                ->message("Setting updated successfully!")
                ->title('⚙️ Configuration Updated')
                ->field($settingName, $displayValue)
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

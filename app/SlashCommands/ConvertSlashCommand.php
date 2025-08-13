<?php

namespace App\SlashCommands;

use App\Models\GuildSetting;
use App\Services\LinkConversionService;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Interaction;
use Laracord\Commands\SlashCommand;

class ConvertSlashCommand extends SlashCommand
{

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'convert';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Convert X/Twitter links to X-Cancel links (private response)';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [
        [
            'name' => 'link',
            'description' => 'The X/Twitter link to convert',
            'type' => Option::STRING,
            'required' => true,
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
        // Get the link conversion service from the container
        $linkConverter = app(LinkConversionService::class);
        
        $link = $this->value('link');
        $conversionResult = $linkConverter->convert($link);

        if (!$conversionResult) {
            $interaction->respondWithMessage(
                $this
                    ->message('❌ No X/Twitter links found in the provided text.')
                    ->build(),
                true // ephemeral - only visible to the user
            );
            return;
        }

        // Increment stats
        $guildId = $interaction->guild_id;
        if ($guildId) {
            $guildName = $interaction->guild?->name;
            GuildSetting::incrementStats($guildId, [
                'twitter_conversions' => $conversionResult->stats['twitter'],
                'x_conversions' => $conversionResult->stats['x'],
            ], $guildName);
        }

        // Send ephemeral response with converted links
        $interaction->respondWithMessage(
            $this
                ->message('Here are your converted X-Cancel links:')
                ->title('🔗 X-Cancel Conversion')
                ->content($conversionResult->convertedContent)
                ->build(),
            true // ephemeral - only visible to the user
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

<?php

namespace App\SlashCommands;

use Discord\Parts\Interactions\Interaction;
use Laracord\Commands\SlashCommand;

class InfoSlashCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'xcancel-info';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Get information about X-Cancel and the xcancel.app website';

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
        $interaction->respondWithMessage(
            $this
                ->message('Learn more about X-Cancel')
                ->title('🔗 X-Cancel Information')
                ->content('X-Cancel is a service that provides better embeds for X/Twitter links by removing tracking parameters and improving privacy.')
                ->field('🌐 Website', 'https://xcancel.app', true)
                ->field('📱 How it works', 'Converts x.com and twitter.com links to xcancel.app equivalents', true)
                ->field('✨ Benefits', '• Better embeds\n• Removes tracking\n• Privacy focused\n• No rate limits', false)
                ->field('🤖 Bot Commands', '• `/convert` - Manually convert links\n• `/xcancel-config` - Configure auto-conversion', false)
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
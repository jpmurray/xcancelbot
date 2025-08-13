<?php

namespace App\Traits;

use Discord\Parts\Interactions\Interaction;

trait HasAdminCheck
{
    protected function isUserAdmin(Interaction $interaction): bool
    {
        $userId = $interaction->user->id;
        $guildId = $interaction->guild_id;
        
        // Check if user is in configured admins list
        $configAdmins = config('discord.admins', []);
        if (in_array($userId, $configAdmins)) {
            return true;
        }
        
        // Check if user is server owner
        $guild = $interaction->guild;
        if ($guild && $guild->owner_id === $userId) {
            return true;
        }
        
        return false;
    }
}
<?php

namespace App\Events;

use App\Models\GuildSetting;
use App\Services\LinkConversionService;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event as Events;
use Laracord\Events\Event;

class MessageCreate extends Event
{
    protected $handler = Events::MESSAGE_CREATE;
    
    protected LinkConversionService $linkConverter;

    public function __construct(LinkConversionService $linkConverter)
    {
        $this->linkConverter = $linkConverter;
    }

    public function handle(Message $message, Discord $discord)
    {
        // Don't process messages from bots
        if ($message->author->bot) {
            return;
        }

        // Get guild settings
        $guildId = $message->guild_id;
        $settings = GuildSetting::getForGuild($guildId);

        // Check if bot is enabled for this guild
        if (!$settings->enabled) {
            return;
        }

        $content = $message->content;
        
        // Check if this is a manual conversion command
        if (preg_match('/^!xcancel$/i', trim($content))) {
            // Check if this message is replying to another message
            if ($message->message_reference && $message->message_reference->message_id) {
                $referencedMessageId = $message->message_reference->message_id;
                
                // Fetch the referenced message
                $message->channel->messages->fetch($referencedMessageId)->then(function ($referencedMessage) use ($message, $discord, $settings, $guildId) {
                    if ($referencedMessage && $this->linkConverter->containsTwitterLink($referencedMessage->content)) {
                        $conversionResult = $this->linkConverter->convert($referencedMessage->content);
                        if ($conversionResult) {
                            GuildSetting::incrementStats($guildId, [
                                'twitter_conversions' => $conversionResult->stats['twitter'],
                                'x_conversions' => $conversionResult->stats['x'],
                            ]);
                            $this->sendEmbedResponse($message, $discord, $conversionResult->convertedContent, $settings);
                        }
                    }
                });
            }
            return;
        }
        
        // Auto mode: Check if the message contains X/Twitter links
        if ($settings->auto_mode && $this->linkConverter->containsTwitterLink($content)) {
            $conversionResult = $this->linkConverter->convert($content);
            if ($conversionResult) {
                GuildSetting::incrementStats($guildId, [
                    'twitter_conversions' => $conversionResult->stats['twitter'],
                    'x_conversions' => $conversionResult->stats['x'],
                ]);
                $this->sendEmbedResponse($message, $discord, $conversionResult->convertedContent, $settings);
            }
        }
    }


    /**
     * Send embed response
     */
    private function sendEmbedResponse(Message $message, Discord $discord, string $convertedContent, GuildSetting $settings)
    {
        $embed = $discord->factory(Embed::class);
        $embed->setDescription($convertedContent);
        $embed->setColor(0x1DA1F2); // Twitter blue

        if ($settings->show_credit) {
            $embed->setFooter('Original link shared by ' . $message->author->username);
        }

        $embed->setTimestamp();

        $message->channel->sendMessage(MessageBuilder::new()->addEmbed($embed));
    }

    /**
     * Send text response
     */
    private function sendTextResponse(Message $message, string $convertedContent, GuildSetting $settings)
    {
        $response = $convertedContent;

        if ($settings->show_credit) {
            $response = "Original link shared by " . $message->author->username . "\n" . $convertedContent;
        }

        $message->channel->sendMessage(MessageBuilder::new()->setContent($response));
    }
}

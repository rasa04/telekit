<?php
namespace Core\Methods;

abstract class SendAction
{
    use \Core\Controllers;

    protected $response;

    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     */
    public function chat_id(int $chat_id) : object
    {
        $this->response['chat_id'] = $chat_id;
        return $this;
    }

    /**
     * Mode for parsing entities in the message text. See formatting options for more details.
     */
    public function parse_mode(string $parse_mode = 'html') : object
    {
        $this->response['parse_mode'] = $parse_mode;
        return $this;
    }

    /**
     * If the message is a reply, ID of the original message
     */
    public function reply_to_message_id(int $reply_to_message_id) : object
    {
        $this->response['reply_to_message_id'] = $reply_to_message_id;
        return $this;
    }

    /**
     * Additional interface options. A JSON-serialized object for an inline keyboard, custom reply keyboard, 
     * instructions to remove reply keyboard or to force a reply from the user.
     */
    public function reply_markup(array $reply_markup) : object
    {
        $this->response['reply_markup'] = $reply_markup;
        return $this;
    }
    
    /**
     * Unique identifier for the target message thread (topic) of the forum; for forum supergroups only
     */
    public function message_thread_id(int $message_thread_id) : object
    {
        $this->response['message_thread_id'] = $message_thread_id;
        return $this;
    }

    /**
     * Sends the message silently. Users will receive a notification with no sound.
     */
    public function disable_notification(bool $disable_notification) : object
    {
        $this->response['disable_notification'] = $disable_notification;
        return $this;
    }

    /**
     * Protects the contents of the sent message from forwarding and saving
     */
    public function protect_content(bool $protect_content) : object
    {
        $this->response['protect_content'] = $protect_content;
        return $this;
    }
    
    /**
     * Pass True if the message should be sent even if the specified replied-to message is not found
     */
    public function allow_sending_without_reply(bool $allow_sending_without_reply) : object
    {
        $this->response['allow_sending_without_reply'] = $allow_sending_without_reply;
        return $this;
    }
}
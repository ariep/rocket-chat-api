<?php

namespace RocketChat\Api;

/**
 * Posting, editing and deleting messages.
 *
 * @author Fogarasi Ferenc <ffogarasi at gmail dot com>
 * Website: http://github.com/ffogarasi/rocket-chat-api
 */
class Chat extends AbstractApi
{
    public function sendMessage($id, $message)
    {
        $result = $this->post("chat.postMessage", [ 'roomId' => $id, 'text' => $message]);

        if ($this->status)
        {
            return $result;
        }

        return null;
    }

    public function deleteMessage($roomID, $messageID)
    {
        $result = $this->post("chat.delete", [ 'roomId' => $roomID, 'msgId' => $messageID]);

        if ($this->status)
        {
            return $result;
        }

        return null;
    }
}

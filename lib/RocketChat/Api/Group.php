<?php

namespace RocketChat\Api;

/**
 * Listing users, creating, editing.
 *
 * @author Fogarasi Ferenc <ffogarasi at gmail dot com>
 * Website: http://github.com/ffogarasi/rocket-chat-api
 */
class Group extends AbstractApi
{

    /**
     * Returns tokens for .
     *
     * @param string $name the name of the new group
     * @param array  $usernames the names of the user that should be added to the group
     *
     * @return result of the api call
     */
    public function create($name, $usernames = array())
    {
        $result = $this->post('groups.create', ['name' => $name, 'members' => $usernames]);

        if ($this->status)
        {
            return $result->group;
        }

        return null;
    }


    public function sendMessage($id, $message)
    {
        $result = $this->post("chat.postMessage", [ 'roomId' => $id, 'text' => $message]);

        if ($this->status)
        {
            return $result;
        }

        return null;

    }
    
}

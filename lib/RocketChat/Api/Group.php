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
    
    public function listRooms()
    {
        $result = (object)[
            'groups' => array(),
            'total' => 0,
            'count' => 0,
            'success' =>1,
        ];

        $offset=0;

        do
        {
            $partial = $this->get("groups.list?count=100&offset={$offset}");

            if (!$this->status)
            {
                return null;
            }

            $result->total = (int) $partial->total;
            $result->groups = array_merge($result->groups, $partial->groups);
            $offset+=(int) $partial->count;
        }
        while( count($result->groups) < $result->total);

        return $result;
    }

    public function addMember($roomID, $userID)
    {
        $result = $this->post("groups.invite", [ 'roomId' => $roomID, 'userId' => $userID]);

        if ($this->status)
        {
            return $result;
        }

        return null;
    }

    public function removeMember($roomID, $userID)
    {
        $result = $this->post("groups.kick", [ 'roomId' => $roomID, 'userId' => $userID]);

        if ($this->status)
        {
            return $result;
        }

        return null;
    }

    public function history($id, $from = null, $to = null, $count = 100)
    {
        $params = array('roomId' => $id, 'count' => $count);
        if (! is_null($from))
        {
            $params['from'] = $from;
        }
        if (! is_null($to))
        {
            $params['to'] = $to;
        }
        $queryParts = array();
        foreach($params as $k => $v)
        {
            $queryParts[] = $k . '=' . $v;
        }
        $query = implode('&', $queryParts);
        $result = $this->get('groups.history?' . $query);

        if ($this->status)
        {
            return $result;
        }

        return null;
    }
}

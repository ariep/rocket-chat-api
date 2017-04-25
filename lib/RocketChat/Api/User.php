<?php

namespace RocketChat\Api;

/**
 * Listing users, creating, editing, logging in and out.
 *
 * @author Fogarasi Ferenc <ffogarasi at gmail dot com>
 * Website: http://github.com/ffogarasi/rocket-chat-api
 */
class User extends AbstractApi
{
    private $userList = null;

    /**
     * Returns tokens for user
     *
     * @param bool  $username the username of the user
     * @param array $password the password of the user
     *
     * @return user's auth token and userId
     */
    public function login($username, $password)
    {
        $result = $this->post('login', ['user'=>$username, 'password'=>$password]);

        if ($this->status)
        {
            return $result->data;
        }

        return null;
    }

    public function lookup($username)
    {
        $this->populateUserList();

        if (array_key_exists($username, $this->userList))
        {
            return $this->userList[$username];
        }
        else
        {
            return null;
        }
    }

    private function populateUserList()
    {
        if (! is_null($this->userList))
        {
            return;
        }

        $result = (object)[
            'users' => [],
            'total' => 0,
            'count' => 0,
            'success' =>1,
        ];
        $offset=0;
        do
        {
            $partial = $this->get("users.list?count=100&offset={$offset}");
            if (!$this->status)
            {
                return null;
            }
            $result->total = (int)$partial->total;
            $result->users = array_merge($result->users,$partial->users);
            $offset+=(int)$partial->count;
        }
        while( count($result->users)< $result->total);

        $this->userList = array();
        foreach ($result->users as $user)
        {
            if (isset($user->username))
            {
                $this->userList[$user->username] = $user;
            }
        }
    }
}

?>

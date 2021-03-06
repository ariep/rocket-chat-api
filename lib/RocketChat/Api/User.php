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
    private $usersByName = null;
    private $usersByID   = null;

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

    /**
     * Create a new user
     *
     * @param string $username the username of the user
     * @param string $password the password of the user
     * @param string $name     the display name of the user
     * @param string $email    the email address of the user
     * @param array  $params   extra parameters for rocketchat api call
     *
     * @return the new user's data
     */
    public function create($username, $password, $name, $email, $params = array())
    {
        $params['username'] = $username;
        $params['password'] = $password;
        $params['name'] = $name;
        $params['email'] = $email;
        $result = $this->post('users.create', $params);

        if ($this->status)
        {
            return $result->user;
        }

        return null;
    }

    public function lookup($username)
    {
        $this->populateUserList();

        if (array_key_exists($username, $this->usersByName))
        {
            return $this->usersByName[$username];
        }
        else
        {
            return null;
        }
    }

    public function fromID($ID)
    {
        $this->populateUserList();

        if (array_key_exists($ID, $this->usersByID))
        {
            return $this->usersByID[$ID];
        }
        else
        {
            return null;
        }
    }

    public function listAll()
    {
        $this->populateUserList();

        return $this->usersByName;
    }

    private function populateUserList()
    {
        if (! is_null($this->usersByID))
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

        $this->usersByID = array();
        $this->usersByName = array();
        foreach ($result->users as $user)
        {
            if (isset($user->username))
            {
                $this->usersByName[$user->username] = $user;
                $this->usersByID[$user->_id] = $user;
            }
        }
    }
}

?>

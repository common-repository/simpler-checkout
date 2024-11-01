<?php

namespace Simpler\Models;

class User
{

    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $first_name;
    /**
     * @var string
     */
    private $last_name;

    public function __construct(string $email, string $first_name, string $last_name)
    {
        $this->email      = $email;
        $this->first_name = $first_name;
        $this->last_name  = $last_name;
    }

    public static function from_json(array $json)
    {
        return new User($json['email'], $json['first_name'], $json['last_name']);
    }

    public function get_email()
    {
        return $this->email;
    }

    public function get_first_name()
    {
        return $this->first_name;
    }

    public function get_last_name()
    {
        return $this->last_name;
    }
}

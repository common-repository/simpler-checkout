<?php

namespace Simpler\Services;

use Simpler\Models\User;

final class UserService
{

    public function get_or_create(User $user)
    {
        $existing_user = get_user_by('email', $user->get_email());
        if ($existing_user instanceof \WP_User) {
            $user_id = $this->update_existing_user($user, $existing_user);
        } else {
            $user_id = $this->create_new_user($user);
        }

        if (is_wp_error($user_id)) {
            return null;
        }

        return $user_id;
    }

    private function create_new_user(User $user)
    {
        $userdata = [
            'ID'         => '',
            'user_pass'  => wp_generate_password(),
            'user_login' => 'simpler_' . str_replace('@', '_', $user->get_email()),
            'user_email' => $user->get_email(),
            'first_name' => $user->get_first_name(),
            'last_name'  => $user->get_last_name(),
            'role'       => 'customer'
        ];

        return wp_insert_user(wp_slash($userdata));
    }

    private function update_existing_user(User $new_user, \WP_User $old_user)
    {
        $userdata = [
            'ID'         => $old_user->get('ID'),
            'user_pass'  => $old_user->get('pass'),
            'user_login' => $new_user->get_email(),
            'user_email' => $new_user->get_email(),
            'first_name' => $new_user->get_first_name(),
            'last_name'  => $new_user->get_last_name(),
        ];

        return wp_update_user(wp_slash($userdata));
    }
}

<?php

namespace UWMadison\UserAllowListAPI;

use ExternalModules\AbstractExternalModule;
use RestUtility;

class UserAllowListAPI extends AbstractExternalModule
{
    private $requestingUser;

    public function process()
    {
        $result = [
            'status' => 'failure',
            'message' => 'Invalid token',
            'value' => null
        ];

        $token = $this->sanitizeAPIToken($_POST['token']);
        $action = $_POST['action'];
        $user = $_POST['user'];

        if (strlen($token) !== 64) {
            return $result;
        }

        $q = $this->query("
        SELECT username, super_user
        FROM redcap_user_information
        WHERE api_token = ?
        AND user_suspended_time IS NULL
        LIMIT 1", $token);

        if (!($q && $q !== false && db_num_rows($q) == 1)) {
            return $result;
        }

        $this->requestingUser = db_fetch_assoc($q)["username"];

        if (empty($action) || empty($user)) {
            $result['message'] = 'Missing user or action';
            return $result;
        }

        if (!in_array($action, ['add', 'remove', 'search'])) {
            $result['message'] = 'Invalid action';
            return $result;
        }

        return array_merge(
            ['status' => 'success'],
            $this->$action($user)
        );
    }

    private function add($username)
    {
        $q = $this->query('SELECT * FROM redcap_user_allowlist WHERE username = ?', $username);
        if (db_num_rows($q) > 0) {
            return [
                'message' => 'User already in allow list',
                'value' => false
            ];
        }
        $this->query('INSERT INTO redcap_user_allowlist (username) VALUES (?)', $username);
        return [
            'message' => 'User added to allow list',
            'value' => true
        ];
    }

    private function remove($username)
    {
        $q = $this->query('SELECT * FROM redcap_user_allowlist WHERE username = ?', $username);
        if (db_num_rows($q) === 0) {
            return [
                'message' => 'User not in allow list',
                'value' => false
            ];
        }
        $this->query('DELETE FROM redcap_user_allowlist WHERE username = ?', $username);
        return [
            'message' => 'User removed from allow list',
            'value' => true
        ];
    }

    private function search($username)
    {
        $q = $this->query('SELECT * FROM redcap_user_allowlist WHERE username = ?', $username);
        if (db_num_rows($q) === 0) {
            return [
                'message' => 'User not in allow list',
                'value' => false
            ];
        }
        return [
            'message' => 'User found in allow list',
            'value' => true
        ];
    }
}

<?php

namespace UWMadison\UserAllowListAPI;

use ExternalModules\AbstractExternalModule;
use RestUtility;

class UserAllowListAPI extends AbstractExternalModule
{
    public function process()
    {
        $result = [
            'status' => 'failure',
            'message' => '',
            'value' => null
        ];
        $request = RestUtility::processRequest(true);
        $params = $request->getRequestVars(); // token, user, action
        $action = $params['action'];
        $user = $params['user'];

        // Token was already validated by the API framework
        // we can just check the length
        if (strlen($params['token']) !== 64) {
            $result['message'] = 'Invalid token';
            return $result;
        }

        if (empty($action) || empty($params['action'])) {
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
        $this->query('SELECT * FROM redcap_user_allowlist WHERE username = ?', [$username]);
        if ($this->getRowCount() > 0) {
            return [
                'message' => 'User already in allow list',
                'value' => false
            ];
        }
        $this->query('INSERT INTO redcap_user_allowlist (username) VALUES (?)', [$username]);
        return [
            'message' => 'User added to allow list',
            'value' => true
        ];
    }

    private function remove($username)
    {
        $this->query('SELECT * FROM redcap_user_allowlist WHERE username = ?', [$username]);
        if ($this->getRowCount() === 0) {
            return [
                'message' => 'User not in allow list',
                'value' => false
            ];
        }
        $this->query('DELETE FROM redcap_user_allowlist WHERE username = ?', [$username]);
        return [
            'message' => 'User removed from allow list',
            'value' => true
        ];
    }

    private function search($username)
    {
        $this->query('SELECT * FROM redcap_user_allowlist WHERE username = ?', [$username]);
        if ($this->getRowCount() === 0) {
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

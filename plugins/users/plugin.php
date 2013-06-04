<?php

class users extends Plugin {

    var $name = '';
    var $forceMenus = array();

    function users($db, $site) {
        $this->Plugin($db, $site);

        // initialise an empty user
        $this->site->user = $db->getObject('user', 1);

        if (isset($_GET['logout'])) {
            // user requested logout, clear session cookie
            setcookie('bee_session', '', time() - (60 * 60 * 24 * 30), '/');
            unset($_COOKIE['bee_session']);
        } else if (!empty($_POST['username']) && !empty($_POST['password'])) {
            // user is logging in
            $checkUser = $db->QueryA('select id from user where username = ? and password = ?', array($_POST['username'], md5($_POST['password'])));
            if (is_array($checkUser) && $checkUser[0]['id'] > 0) {
                // generate session and set cookie
                $sessionId = md5(uniqid(time(), true));
                setcookie('bee_session', $sessionId, time() + (60 * 60 * 24 * 30), '/');

                // set session in user
                $this->site->user = $db->getObject('user', $checkUser[0]['id']);
                $this->site->user->session_id = $sessionId;
            }
        } else if (isset($_COOKIE['bee_session'])) {
            // session set, check if it exists
            $checkUser = $db->QueryA('select id from user where session_id = ?', array($_COOKIE['bee_session']));
            if (!is_array($checkUser)) {
                // invalid session, unset
                setcookie('bee_session', '', time() - (60 * 60 * 24 * 30), '/');
                unset($_COOKIE['bee_session']);
            } else {
                $this->site->user = $db->getObject('user', $checkUser[0]['id']);
            }
        }

        // the user is valid
        if ($this->site->user->id > 0) {
            //$this->site->user->activetime = date('Y-m-d H:i:s');
            //$this->site->user->save();
        }
    }

    function getSideBox() {
        if ($this->site->user->id == 0) {
            return $this->render('side_login', array('register' => $GLOBALS['config']['site']['registration']));
        } else {
            return $this->render('side_logged_in', array('user' => $this->site->user->row));
        }
    }

    function getContent() {
        if (!isset($_GET['mode'])) {
            $_GET['mode'] = 'welcome';
        }

        switch ($_GET['mode']) {
            case 'register':
                $this->name = 'User Registration';
                return $this->register();
            case 'edit':
                $this->name = 'Preferences';
                return $this->edit();
            default:
                return $this->welcome();
        }
    }

    function register() {
        if (!$GLOBALS['config']['site']['registration']) {
            return '<h1>New user registrations are disabled!</h1>';
        } else {
            $_POST['username'] = trim($_POST['email']);
            $_POST['email'] = trim($_POST['email']);
            $_POST['password'] = trim($_POST['password']);

            if (empty($_POST['username']) || empty($_POST['password'])) {
                return $this->render('register', array());
            } else {
                $user = DB::getInstance()->getObject('user', $_POST['username'], 'username');
                if ($user->id > 0) {
                    return $this->render('register', array('error' => array('message' => 'User name ' . $_POST['username'] . ' already in use.')));
                } else {
                    $user->username = $_POST['email'];
                    $user->password = md5($_POST['password']);
                    $user->email = $_POST['email'] . ' ';
                    $user->level = 1;
                    $user->save();
                    return $this->render('register_ok', array('name' => $_POST['username']));
                }
            }
        }
    }

    function edit() {
        if ($this->site->user->id == 0) {
            return '<h2>DENIED!</h2>';
        }

        if (isset($_POST['save'])) {
            if (!empty($_POST['password'])) {
                $this->site->user->password = md5($_POST['password']);
            }
            $this->site->user->email = $_POST['email'];
            $this->site->user->save();
        }

        return $this->render('edit', array('user' => $this->site->user->row));
    }

    function welcome() {
        // welcome page when a user logs in

        return $this->render('welcome', array());
    }

}

?>
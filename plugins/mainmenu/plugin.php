<?php

class mainmenu extends Plugin {

    var $name = 'Main Menu';
    var $level = 1;
    var $links = array();

    function mainmenu($site) {
        $this->Plugin($site);

        $this->addGroup('Main Menu', 'main');

        if ($site->user->level >= 5) {
            $this->addGroup('Configuration', 'config');
        }
    }

    function getSideBox() {
        $smallicons = true;
        return $this->render('menu', array('links' => $this->links, 'smallicons' => $smallicons));
    }

    function addGroup($title, $name) {
        if (!isset($this->links[$name])) {
            $this->links[$name] = array();
            $this->links[$name]['links'] = array();
        }
        $this->links[$name]['title'] = $title;
    }

    function addLink($group, $title, $url, $icon = '', $ext = false) {
        if (!$ext) {
            $url = $GLOBALS['config']['site']['url'] . '/' . $url;
        }
        if (isset($this->links[$group])) {
            $this->links[$group]['links'][] = array('t' => $title, 'l' => $url, 'i' => $icon);
        }
    }

    function hasGroup($name) {
        return isset($this->links[$name]);
    }

    function hasLink($group, $title) {
        if (isset($this->links[$group])) {
            for ($i = 0; $i < count($this->links[$group]['links']); $i++) {
                if (strtolower($this->links[$group]['links'][$i]['t']) == strtolower($title)) {
                    return true;
                }
            }
        }
        return false;
    }

}

?>
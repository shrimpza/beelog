<?php

class setprojects extends Plugin {

    var $name = 'Project Configuration';
    var $level = 5;
    
    
    function setprojects($site) {
        $this->Plugin($site);

        $this->site->plugins['mainmenu']->addLink('config', 'Projects', '?module=setprojects', 'config_projects');
    }
    
    function getContent() {
        return $this->projectList();
    }
    
    function projectList() {
        $projects = objectToArray(DB::getInstance()->getObjects('project', '', 'name'));

        return $this->render('projects', array('projects' => $projects));
    }
}

?>

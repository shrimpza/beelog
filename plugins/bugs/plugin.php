<?php

    class bugs extends Plugin {

        var $name = 'Bugs';
        var $level = 0;

        function bugs($site) {
            $this->Plugin($site);

            $this->site->plugins['mainmenu']->addLink('main', 'Bugs List', '?module=bugs', 'bugs_list');
        }

        function getContent() {
            return $this->bugList();
        }

        function bugList($projectId = 0) {
            if ($projectId > 0) {
                $project = DB::getObject('project', $projectId);
                $bugs = $project->get_bug_list();
            } else {
                $bugs = DB::getObjects('bug');
            }

            if ($bugs) {
                for ($i = 0; $i < count($bugs); $i++) {
                    $bugs[$i]->loadRelations();
                }
            }

            return $this->render('list', array('bugs' => objectToArray($bugs)));
        }

    }

?>

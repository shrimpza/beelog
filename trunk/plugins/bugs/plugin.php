<?php

    class bugs extends Plugin {

        var $name = 'Bugs';
        var $level = 0;

        function bugs($site) {
            $this->name = $GLOBALS['config']['bugs']['desc_plural'];

            $this->Plugin($site);

            $this->site->plugins['mainmenu']->addLink('main', 'List ' . $this->name, '?module=bugs', 'bugs_list');
            $this->site->plugins['mainmenu']->addLink('main', 'New ' . $GLOBALS['config']['bugs']['desc'], '?module=bugs&edit=0', 'bugs_new');
        }

        function getContent() {
            if (isset($_GET['edit'])) {
                return $this->editBug($_GET['edit']);
            } else {
                return $this->bugList();
            }
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

        function editBug($id = 0) {
            $bug = DB::getObject('bug', $_GET['edit']);

            $priority = DB::getObjects('priority', '', 'display_order desc');
            $status = DB::getObjects('status', '', 'display_order');
            $projects = DB::getObjects('project', '', 'name');

            return $this->render('edit', array(
                        'b' => objectToArray($bug),
                        'priority' => objectToArray($priority),
                        'status' => objectToArray($status),
                        'projects' => objectToArray($projects)));
        }

        function getContentJson() {
            if (isset($_GET['project'])) {
                $project = DB::getObject('project', $_GET['project']);
                $initiatives = $project->get_initiative_list();
                return json_encode($initiatives);
            }
        }

    }

?>

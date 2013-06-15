<?php

    class projects extends Plugin {

        var $name = 'Project Configuration';
        var $level = 5;

        function projects($site) {
            $this->Plugin($site);

            $this->site->plugins['mainmenu']->addLink('config', 'Projects', '?module=projects', 'config_projects');
        }

        function getContent() {
            if (isset($_GET['edit'])) {
                $project = objectToArray(DB::getObject('project', $_GET['edit']));
                return $this->render('project', array('p' => $project));
            } else if (isset($_POST['id'])) {
                $project = DB::getObject('project', $_POST['id']);

                $project->name = trim($_POST['name']);
                $project->slug = slugify($project->name);

                $project->save();

                return $this->projectList();
            } else {
                return $this->projectList();
            }
        }

        function projectList() {
            $projects = objectToArray(DB::getObjects('project', '', 'name'));

            return $this->render('projects', array('projects' => $projects));
        }

    }

?>

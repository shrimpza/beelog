<?php

    class projects extends Plugin {

        var $name = 'Project Configuration';
        var $level = 5;

        function projects($site) {
            $this->Plugin($site);

            $this->site->plugins['mainmenu']->addLink('config', 'Projects', '?module=projects', 'projects');
        }

        function getContent() {
            if (isset($_GET['edit'])) {
                return $this->showProject($_GET['edit']);
            } else if (isset($_GET['delete'])) {
                $project = DB::getObject('project', $_GET['delete']);
                $project->delete();

                return $this->projectList();
            } else if (isset($_POST['id'])) {
                $project = DB::getObject('project', $_POST['id']);

                $project->name = trim($_POST['name']);
                $project->slug = slugify($project->name);

                $project->save();

                $intitIds = $_POST['i_id'];
                $intitNames = $_POST['i_name'];
                $intitDel = $_POST['i_del'];
                for ($i = 0; $i < count($intitIds); $i++) {
                    $initiative = DB::getObject('initiative', $intitIds[$i]);
                    if ($intitDel[$i] > 0) {
                        $initiative->delete();
                    } else {
                        $initiative->project_id = $project->id;
                        $initiative->name = trim($intitNames[$i]);
                        $initiative->slug = slugify($initiative->name);

                        $initiative->save();
                    }
                }

                return $this->showProject($project->id);
            } else {
                return $this->projectList();
            }
        }

        function showProject($id) {
            $project = DB::getObject('project', $id);
            $project->initiatives = objectToArray($project->get_initiative_list());

            return $this->render('project', array('p' => objectToArray($project)));
        }

        function projectList() {
            $projects = objectToArray(DB::getObjects('project', '', 'name'));

            return $this->render('projects', array('projects' => $projects));
        }

    }

?>

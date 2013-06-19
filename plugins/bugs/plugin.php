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
            if (isset($_GET['view'])) {
                return $this->viewBug($_GET['view']);
            } else if (isset($_GET['edit'])) {
                return $this->editBug($_GET['edit']);
            } else if (isset($_POST['id'])) {
                $bug = $this->saveBug($_POST);
                return $this->viewBug($bug->id);
            } else {
                return $this->bugList();
            }
        }

        function saveBug($src) {
            $bug = DB::getObject('bug', $src['id']);

            $bug->fromArray($src);
            $bug->slug = slugify($bug->title);
            $bug->updated_date = time();

            if ($bug->id == 0) {
                $bug->user_id = $this->site->user->id;
                $bug->created_date = time();
            }

            if (!$bug->initiative_id) {
                $bug->initiative_id = null;
            }
            if (!$bug->assigned_user_id) {
                $bug->assigned_user_id = null;
            }
            if (!$bug->status_id) {
                $status = DB::getObjects('status', 'closed = 0', 'display_order');
                $bug->status_id = $status[0]->id;
            }

            if ($bug->save()) {
                $project = DB::getObject('project', $bug->project_id);
                $project->counter += 1;
                $project->save();

                $bug->ident = $project->slug . '-' . ($project->counter);
                $bug->save();
            }

            return $bug;
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

        function viewBug($id) {
            $bug = DB::getObject('bug', $id);
            $bug->loadRelations();

            return $this->render('view', array('b' => objectToArray($bug)));
        }

        function editBug($id = 0) {
            $bug = DB::getObject('bug', $id);

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

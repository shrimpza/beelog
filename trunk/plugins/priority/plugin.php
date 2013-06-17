<?php

    class priority extends Plugin {

        function priority($site) {
            $this->Plugin($site);

            $this->site->plugins['mainmenu']->addLink('config', 'Priorities', '?module=priority', 'priority');
        }

        function getContent() {
            if (isset($_GET['edit'])) {
                $priority = DB::getObject('priority', $_GET['edit']);

                return $this->render('priority', array('p' => objectToArray($priority)));
            } else if (isset($_GET['delete'])) {
                $priority = DB::getObject('priority', $_GET['delete']);
                $priority->delete();

                return $this->priorityList();
            } else if (isset($_POST['id'])) {
                $priority = DB::getObject('priority', $_POST['id']);

                $priority->name = trim($_POST['name']);
                $priority->display_order = trim($_POST['display_order']);
                $priority->colour = trim($_POST['colour']);

                $priority->save();

                return $this->priorityList();
            } else {
                return $this->priorityList();
            }
        }

        function priorityList() {
            $priorities = objectToArray(DB::getObjects('priority', '', 'display_order desc'));

            return $this->render('priorities', array('priorities' => $priorities));
        }

    }

?>

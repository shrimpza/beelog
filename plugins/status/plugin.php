<?php

    class status extends Plugin {

        var $name = 'Status Configuration';
        var $level = 5;

        function status($site) {
            $this->Plugin($site);

            $this->site->plugins['mainmenu']->addLink('config', 'Statuses', '?module=status', 'status');
        }

        function getContent() {
            if (isset($_GET['edit'])) {
                $status = DB::getObject('status', $_GET['edit']);

                return $this->render('status', array('s' => objectToArray($status)));
            } else if (isset($_GET['delete'])) {
                $status = DB::getObject('status', $_GET['delete']);
                $status->delete();

                return $this->statusList();
            } else if (isset($_POST['id'])) {
                $status = DB::getObject('status', $_POST['id']);

                $status->name = trim($_POST['name']);
                $status->display_order = trim($_POST['display_order']);
                $status->closed = isset($_POST['closed']) ? 1 : 0;

                $status->save();

                return $this->statusList();
            } else {
                return $this->statusList();
            }
        }

        function statusList() {
            $status = objectToArray(DB::getObjects('status', '', 'display_order'));

            return $this->render('statuses', array('status' => $status));
        }

    }

?>

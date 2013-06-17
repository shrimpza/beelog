<?php

    class bugs extends Plugin {

        var $name = 'Bugs';
        var $level = 0;

        function bugs($site) {
            $this->Plugin($site);

            $this->site->plugins['mainmenu']->addLink('main', 'Bugs List', '?module=bugs', 'bugs_list');
        }

        function getContent() {
            
        }

    }

?>

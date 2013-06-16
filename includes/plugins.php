<?php

    class Plugin {

        var $name = 'Base Plugin';
        var $_options = array();
        var $level = 0;

        function Plugin($site) {
            $this->site = $site;
        }

        function render($template, $vars) {
            $smarty = new Smarty();

            $smarty->setTemplateDir($GLOBALS['config']['plugins']['directory'] . '/' . get_class($this) . '/templates');
            $smarty->setCompileDir($GLOBALS['config']['templates']['compile_dir']);

            foreach ($vars as $var => $value) {
                $smarty->assign($var, $value);
            }

            $smarty->assign('site_url', $GLOBALS['config']['site']['url']);
            $smarty->assign('url_params', $_GET);
            $smarty->assign('theme', $GLOBALS['config']['templates']['theme']);

            return $smarty->fetch($template . '.html');
        }

//      function getContent() {
//      }
//
//      function getContentJson() {
//      }
//
//      function getSideBox() {
//      } 
    }

?>
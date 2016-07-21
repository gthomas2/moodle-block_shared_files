<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Print shared files tree
 *
 * @package    block_shared_files
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_shared_files_renderer extends plugin_renderer_base {

    /**
     * Prints shared files tree view
     * @return string
     */
    public function shared_files_tree() {
        return $this->render(new shared_files_tree);
    }

    public function render_shared_files_tree(shared_files_tree $tree) {
        global $PAGE;
        $module = array('name'=>'block_shared_files', 'fullpath'=>'/blocks/shared_files/module.js', 'requires'=>array('yui2-treeview'));
        $html = '';
        foreach($tree->areas as $id=> $areadata) {
          $area = $areadata['tree'];
          $name = $areadata['name'];
          $html .= '<h3>' . $name . '</h3>';
          if (empty($area['subdirs']) && empty($area['files'])) {
              $html .= $this->output->box(get_string('nofilesavailable', 'repository'));
          } else {
              $htmlid = 'shared_files_tree_'.uniqid();
              $this->page->requires->js_init_call('M.block_shared_files.init_tree', array(false, $htmlid));
              $html .= '<div id="'.$htmlid.'">';
              $html .= $this->htmllize_tree($area, $id);

              $html .= '</div>';
          }
          $html .= html_writer::link(
            new moodle_url('/blocks/shared_files/files.php',
                           array('returnurl' => $PAGE->url->out(), 'areaid' => $id)),
            get_string('sharedfilesmanage', 'block_shared_files') . ' ' . $name);
        }

        return $html;
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     */
    protected function htmllize_tree($dir, $areaid) {
        global $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon(file_folder_icon(), $subdir['dirname'], 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.s($subdir['dirname']).'</div> '.$this->htmllize_tree($subdir, $areaid).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/1/block_shared_files/shared'.$areaid.$file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
            $image = $this->output->pix_icon(file_file_icon($file), $filename, 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.html_writer::link($url, $image.$filename).'</div></li>';
        }
        $result .= '</ul>';

        return $result;
    }
}

class shared_files_tree implements renderable {
    public $context;
    public $dir;
    public function __construct() {
        global $USER, $DB;
        $this->context = context_user::instance($USER->id);
        $fs = get_file_storage();
        $user_areas = $DB->get_records_sql('SELECT areas.name, areas.id, areas.global ' .
          'FROM {shared_files_areas} as areas, {shared_files_usage} as usage ' .
          'WHERE areas.id = usage.areaid AND usage.userid = ?', array($USER->id));
        $this->areas = array();
        foreach($user_areas as $area) {
          $this->areas[$area->id] = array('name' => $area->name, 'tree' => $fs->get_area_tree(context_system::instance()->id, 'block_shared_files', 'shared' . $area->id, 0));
        }
    }
}

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
 * minimalistic edit form
 *
 * @package   core_user
 * @category  files
 * @copyright 2016 Dimitri Vorona
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
  Class shared_files form for adding a new area
  @copyright 2016 Dimitri Vorona
  @licence http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

class shared_files_addarea_form extends moodleform {
    /**
     * Add element to this form
    */
    public function definition() {
        $mform = $this->_form;

        $data = $this->_customdata['data'];
        $mform->addElement('text', 'areaname', get_string('areaname', 'block_shared_files'), null);
        $mform->setType('areaname', PARAM_TEXT);
        $this->add_action_buttons(true, get_string('savechanges'));
        $mform->addElement('hidden', 'returnurl', $data->returnurl);
        $mform->setType('returnurl', PARAM_LOCALURL);

        $this->set_data($data);
    }
}

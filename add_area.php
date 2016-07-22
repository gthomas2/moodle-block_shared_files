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
 * Manage files in folder in private area.
 *
 * @package   core_user
 * @category  files
 * @copyright 2016 Dimitri Vorona
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/blocks/shared_files/add_area_form.php");
// require_once("$CFG->dirroot/repository/lib.php");

require_login();
if (isguestuser()) {
    die();
}

$context = context_system::instance();
require_capability('moodle/user:manageownfiles', $context);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

$title = get_string('sharedfiles', 'block_shared_files');  // TODO: change title
$struser = get_string('user');

$PAGE->set_url('/blocks/shared_files/addarea.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading(fullname($USER));
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('shared-files');

$data = new stdClass();
$data->returnurl = $returnurl;

$mform = new shared_files_addarea_form(null, array('data' => $data));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()){
    global $DB, $USER;
    $new_area_name = $formdata->areaname;
    $area_record = new stdClass();
    $area_record->name = $new_area_name;
    $area_record->global = 0;
    $area_id = $DB->insert_record('shared_files_areas', $area_record);
    $owner_record = new stdClass();
    $owner_record->areaid = $area_id;
    $owner_record->userid = $USER->id;
    $DB->insert_record('shared_files_usage', $owner_record);
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

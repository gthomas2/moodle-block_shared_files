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
require_once("$CFG->dirroot/blocks/shared_files/files_form.php");
require_once("$CFG->dirroot/repository/lib.php");

require_login();
if (isguestuser()) {
    die();
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$areaid = optional_param('areaid', '', PARAM_INT);

if (empty($returnurl)) {
    $returnurl = new moodle_url('/shared/files.php');
}

$context = context_system::instance();
require_capability('moodle/user:manageownfiles', $context);

$title = get_string('sharedfiles', 'block_shared_files');
$struser = get_string('user');

$PAGE->set_url('/blocks/shared_files/files.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading(fullname($USER));
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('shared-files');

$maxbytes = $CFG->userquota;
$maxareabytes = $CFG->userquota;
if (has_capability('moodle/user:ignoreuserquota', $context)) {
    $maxbytes = USER_CAN_IGNORE_FILE_SIZE_LIMITS;
    $maxareabytes = FILE_AREA_MAX_BYTES_UNLIMITED;
}

$data = new stdClass();
$data->returnurl = $returnurl;
$data->areaid = $areaid;
$options = array('subdirs' => 1, 'maxbytes' => $maxbytes, 'maxfiles' => -1, 'accepted_types' => '*',
        'areamaxbytes' => $maxareabytes);
file_prepare_standard_filemanager($data, 'files', $options, $context, 'block_shared_files', 'shared' . $areaid, 0);

/*
// Attempt to generate an inbound message address to support e-mail to private files.
$generator = new \core\message\inbound\address_manager();
$generator->set_handler('\core\message\inbound\private_files_handler');
$generator->set_data(-1);
$data->emaillink = $generator->generate($USER->id);
*/

$mform = new shared_files_form(null, array('data' => $data, 'options' => $options));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {

    $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, 'block_shared_files', 'shared' . $formdata->areaid, 0);
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');


$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

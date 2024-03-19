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
 * Page to allow user to delete a model
 *
 * @package   mod_wavefront
 * @copyright 2022 Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

$id     = required_param('id', PARAM_INT);         // Model id.
$cmid   = required_param('cmid', PARAM_INT);       // Course Module ID.
$delete = optional_param('d', '', PARAM_ALPHANUM); // Delete confirmation hash.

if (!$cm = get_coursemodule_from_id('wavefront', $cmid)) {
    throw new moodle_exception('invalidcoursemodule');
}

$context = context_module::instance($cm->id);

require_capability('mod/wavefront:delete', $context);

if (! $model = $DB->get_record("wavefront_model", array("id" => $id))) {
    throw new moodle_exception('invalidmodelid', 'mod_wavefront');
}

require_login($cm->course, false, $cm);

$PAGE->set_url('/mod/wavefront/delete_model.php', array('id' => $id));
$PAGE->set_title(get_string('deletemodel', 'mod_wavefront'));
$PAGE->set_heading(get_string('deletemodel', 'mod_wavefront'));
$PAGE->navbar->add(get_string('deletemodel', 'mod_wavefront'));

echo $OUTPUT->header();

if (! $delete) {
    $strdeletemodelcheck = get_string("deletemodelcheck", "mod_wavefront");

    $message = "$strdeletemodelcheck<br /><br />" . format_text($model->description, FORMAT_MOODLE);

    $cancelurl = new moodle_url("/mod/wavefront/view.php", array('id' => $cmid, 'editing' => 1));

    echo $OUTPUT->confirm($message, "delete_model.php?id=$model->id&cmid=".$cmid."&d=".md5($model->timemodified), $cancelurl);

    echo $OUTPUT->footer();
    exit;
}

if ($delete != md5($model->timemodified)) {
    throw new moodle_exception("invalidmd5");
}

if (!confirm_sesskey()) {
    throw new moodle_exception('confirmsesskeybad', 'error');
}

// OK checks done, delete the model now.

echo html_writer::tag('p', get_string('deletemodelfiles', 'mod_wavefront'));

$fs = get_file_storage();
$fsfiles = $fs->get_area_files($context->id, 'mod_wavefront', 'model', $model->id, "itemid, filepath, filename", false);

foreach ($fsfiles as $f) {
    $f->delete();
}

echo html_writer::tag('p', get_string('deletemodelrecord', 'mod_wavefront'));

$DB->delete_records("wavefront_model", array("id" => $model->id));

// Trigger deletion event.
$eventparams = array(
    'context' => $context,
    'objectid' => $model->id,
);
$event = \mod_wavefront\event\model_deleted::create($eventparams);
$event->trigger();

echo $OUTPUT->heading(get_string("deletedmodel", "mod_wavefront"));

echo $OUTPUT->continue_button("view.php?id=$cmid&editing=1");

echo $OUTPUT->footer();



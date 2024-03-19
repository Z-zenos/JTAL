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
 * Form for editing a model
 *
 * @package   mod_wavefront
 * @copyright 2017 onward Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$cmid = required_param('cmid', PARAM_INT);            // Course Module ID.
$id   = optional_param('id', 0, PARAM_INT);           // Model ID.

if (!$cm = get_coursemodule_from_id('wavefront', $cmid)) {
    throw new moodle_exception('invalidcoursemodule');
}

// Attempt to get the correct model.
$model = $DB->get_record('wavefront_model', array('id' => $id));

if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    throw new moodle_exception('coursemisconf');
}

$context = context_module::instance($cm->id);

if (!$wavefront = $DB->get_record('wavefront', array('id' => $cm->instance))) {
    throw new moodle_exception('invalidwavefrontid', 'wavefront');
}

$url = new moodle_url('/mod/wavefront/edit_model.php', array('cmid' => $cm->id));
if (!empty($id)) {
    $url->param('id', $id);
}
$PAGE->set_url($url);

require_login($course, false, $cm);

if (!(has_capability('mod/wavefront:edit', $context) || has_capability('mod/wavefront:submit', $context)) ) {
    throw new moodle_exception('nopermissions');
}


if ($model) {
    if (isguestuser()) {
        throw new moodle_exception('guestnoedit', 'wavefront', "$CFG->wwwroot/mod/wavefront/view.php?id=$cmid");
    }

} else { // New entry? Or something has gone horribly wrong...
    $model = new stdClass();
    $model->id = null;
}

$maxfiles = 50;                // TODO: add some setting.
$maxbytes = $course->maxbytes; // TODO: add some setting.

$descriptionoptions = array('trusttext' => true, 'maxfiles' => $maxfiles,
                            'maxbytes' => $maxbytes, 'context' => $context,
                            'subdirs' => file_area_contains_subdirs($context, 'mod_wavefront', 'model', $model->id));
$modeloptions = array('subdirs' => false, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);

$model = file_prepare_standard_editor($model, 'description', $descriptionoptions, $context,
                                      'mod_wavefront', 'description', $model->id);
$model = file_prepare_standard_filemanager($model, 'model', $modeloptions, $context,
                                      'mod_wavefront', 'model', $model->id);

$model->cmid = $cm->id;

// Create form and set initial data.
$mform = new mod_wavefront_edit_model_form(
    null, array('model' => $model, 'cm' => $cm, 'descriptionoptions' => $descriptionoptions,
    'modeloptions' => $modeloptions)
);

if ($mform->is_cancelled()) {
    if ($id) {
        $params = array('id' => $cm->id, 'hook' => $id);
        if (has_capability('mod/wavefront:edit', $context) ) {
            $params['editing'] = 1;
        }
        $url = new moodle_url('/mod/wavefront/view.php', $params);
        redirect($url);
    } else {
        $params = array('id' => $cm->id);
        if (has_capability('mod/wavefront:edit', $context) ) {
            $params['editing'] = 1;
        }
        $url = new moodle_url('/mod/wavefront/view.php', $params);
        redirect($url);
    }

} else if ($model = $mform->get_data()) {
    $timenow = time();

    if (empty($model->id)) {
        $model->wavefrontid      = $wavefront->id;
        $model->userid           = $USER->id;
        $model->timecreated      = $timenow;

        $isnewentry              = true;
    } else {
        $isnewentry              = false;
    }

    $model->description      = '';          // Updated later.
    $model->descriptionformat = FORMAT_HTML; // Updated later.
    $model->timemodified     = $timenow;

    if ($isnewentry) {
        // Add new entry.
        $model->id = $DB->insert_record('wavefront_model', $model);
    } else {
        // Update existing entry.
        $DB->update_record('wavefront_model', $model);
    }

    // Save and relink embedded images and save attachments.
    $model = file_postupdate_standard_editor($model, 'description', $descriptionoptions,
                                             $context, 'mod_wavefront', 'description', $model->id);
    $model = file_postupdate_standard_filemanager($model, 'model', $modeloptions,
                                             $context, 'mod_wavefront', 'model', $model->id);

    wavefront_check_for_zips($context, $cm, $model);

    // Store the updated value values.
    $DB->update_record('wavefront_model', $model);

    // Refetch complete entry.
    $model = $DB->get_record('wavefront_model', array('id' => $model->id));

    // Trigger event and update completion (if entry was created).
    $eventparams = array(
        'context' => $context,
        'objectid' => $model->id,
    );
    if ($isnewentry) {
        $event = \mod_wavefront\event\model_created::create($eventparams);
    } else {
        $event = \mod_wavefront\event\model_updated::create($eventparams);
    }
    $event->add_record_snapshot('wavefront', $wavefront);
    $event->trigger();
    if ($isnewentry) {
        // Update completion state.
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC && $wavefront->completionentries) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        }
    }

    $params = array('id' => $cm->id);
    if (has_capability('mod/wavefront:edit', $context) ) {
        $params['editing'] = 1;
    }
    $url = new moodle_url('/mod/wavefront/view.php', $params);
    redirect($url);
}

if (!empty($id)) {
    $PAGE->navbar->add(get_string('edit'));
}

$PAGE->set_title($wavefront->name);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($wavefront->name), 2);
if ($wavefront->intro) {
    echo $OUTPUT->box(format_module_intro('wavefront', $wavefront, $cm->id), 'generalbox', 'intro');
}

$mform->display();

echo $OUTPUT->footer();


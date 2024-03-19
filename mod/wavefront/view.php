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
 * Prints a particular instance of a 3d model
 *
 * @package   mod_model
 * @copyright 2017 Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/filelib.php');

global $DB;

$id = optional_param('id', 0, PARAM_INT);
$w = optional_param('l', 0, PARAM_INT);
$editing = optional_param('editing', 0, PARAM_BOOL);

if ($id) {
    list($course, $cm) = get_course_and_cm_from_cmid($id, 'wavefront');
    if (!$wavefront = $DB->get_record('wavefront', array('id' => $cm->instance))) {
        throw new moodle_exception('invalidcoursemodule');
    }
} else {
    if (!$wavefront = $DB->get_record('wavefront', array('id' => $w))) {
        throw new moodle_exception('invalidwavefrontid', 'wavefront');
    }
    list($course, $cm) = get_course_and_cm_from_instance($wavefront, 'wavefront');
}


require_login($course, true, $cm);

$PAGE->set_pagelayout('incourse');

$context = context_module::instance($cm->id);

if ($editing) {
    require_capability('mod/wavefront:edit', $context);
}

if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

wavefront_config_defaults();

$params = array(
    'context' => $context,
    'objectid' => $wavefront->id
);
$event = \mod_wavefront\event\course_module_viewed::create($params);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('wavefront', $wavefront);
$event->trigger();

// Mark viewed.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_cm($cm, $course, $wavefront);
$PAGE->set_url('/mod/wavefront/view.php', array('id' => $cm->id));
$PAGE->set_title($wavefront->name);
$PAGE->set_heading($course->shortname);
$button = '';
if (has_capability('mod/wavefront:edit', $context)) {
    $urlparams = array('id' => $id, 'editing' => $editing ? '0' : '1');
    $url = new moodle_url('/mod/wavefront/view.php', $urlparams);
    $strediting = get_string('turnediting'.($editing ? 'off' : 'on'));
    $button = $OUTPUT->single_button($url, $strediting, 'get').' ';
}
$PAGE->set_button($button);

$output = $PAGE->get_renderer('mod_wavefront');

$heading = get_string('displayingmodel', 'wavefront', $wavefront->name);
echo $output->heading($heading);

echo $output->header();

echo html_writer::start_div('wavefront-gallery row');
// Get all models associated with this gallery.
if ($models = $DB->get_records('wavefront_model', array('wavefrontid' => $wavefront->id))) {

    foreach ($models as $model) {

        // Create a unique stage name, which will need to be passed to JS.
        $stagename = uniqid('wavefront_');
        echo $output->display_model($context, $model, $stagename, $editing);
    }
}
echo html_writer::end_div();

if ( has_capability('mod/wavefront:submit', $context) ) {
    $url = new moodle_url('/mod/wavefront/edit_model.php');
    echo '<form action="'. $url . '">'.
        '<input type="hidden" name="cmid" value="'.$PAGE->cm->id.'" />'.
        '<input class="btn btn-secondary" type="submit" Value="'.get_string('addmodel', 'wavefront').'" />'.
        '</form>';
}

echo $output->display_comments($wavefront, $editing);

echo $output->footer();


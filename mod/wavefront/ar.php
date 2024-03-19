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
 * @package   mod_wavefront
 * @copyright 2022 Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/filelib.php');

global $DB;

$m = required_param('m', PARAM_INT);
$w = required_param('w', PARAM_INT);

if (!$wavefront = $DB->get_record('wavefront', array('id' => $w))) {
    throw new moodle_exception('invalidwavefrontid', 'wavefront');
}
list($course, $cm) = get_course_and_cm_from_instance($wavefront, 'wavefront');

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}

wavefront_config_defaults();

$PAGE->set_cm($cm, $course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/wavefront/ar.php', array('id' => $cm->id));
$PAGE->set_title($wavefront->name);
$PAGE->set_pagelayout('popup');

$output = $PAGE->get_renderer('mod_wavefront');

// Send page header.
echo $output->header();

if ($model = $DB->get_record('wavefront_model', array('id' => $m)) ) {

    echo $output->display_model_in_ar($context, $model, 'stage');
}
echo $output->footer();

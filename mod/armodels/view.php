<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_armodels.
 *
 * @package     mod_armodels
 * @copyright   2023 Hoang Anh Tuan <hoanganhtuanbk2001@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$a = optional_param('a', 0, PARAM_INT);

if ($id) {
	$cm = get_coursemodule_from_id('armodels', $id, 0, false, MUST_EXIST);
	$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$moduleinstance = $DB->get_record('armodels', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
	$moduleinstance = $DB->get_record('armodels', array('id' => $a), '*', MUST_EXIST);
	$course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
	$cm = get_coursemodule_from_instance('armodels', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

// $event = \mod_armodels\event\course_module_viewed::create(array(
//     'objectid' => $moduleinstance->id,
//     'context' => $modulecontext
// ));
// $event->add_record_snapshot('course', $course);
// $event->add_record_snapshot('armodels', $moduleinstance);
// $event->trigger();

$PAGE->set_url('/mod/armodels/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$output = $PAGE->get_renderer('mod_armodels');

$searchform = new \mod_armodels\form\search_model_form();

if($data = $searchform->get_data()) {
    $query = required_param('search', PARAM_TEXT);

    if(!empty($query)) {
		redirect(new moodle_url('/mod/armodels/view.php',  ['id' => $cm->id, 'search' => $query]));
    }
} 

echo $output->header();

/* --- GET SKETCHFAB API --- */

$api_url = "https://api.sketchfab.com/v3/models";
$json_data = file_get_contents($api_url);
$response_data = json_decode($json_data, true);
$models = $response_data["results"];
// $models = [];

$heading = "AR Models Plugin created by Hoang Anh Tuan";

$header_rdrb = new \mod_armodels\output\layout($heading);
echo $output->render($header_rdrb);

$searchform->display();

$model_list_rdrb = new \mod_armodels\output\model_list($models);
echo $output->render($model_list_rdrb);

echo $output->footer();
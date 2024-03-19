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
 * Shows a list of available models
 *
 * @package   mod_wavefront
 * @copyright 2017 onward Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$context = context_course::instance($course->id);
require_course_login($course);

$event = \mod_wavefront\event\course_module_instance_list_viewed::create(
    array(
    'context' => $context
    )
);
$event->add_record_snapshot('course', $course);
$event->trigger();

$PAGE->set_url('/mod/wavefront/index.php', array('id' => $id));
$PAGE->set_title($course->fullname);
$PAGE->set_heading($course->shortname);

if (! $models = get_all_instances_in_course('wavefront', $course)) {
    notice("There are no wavefront models", "../../course/view.php?id=$course->id");
    die;
}

echo $OUTPUT->header();

$usesections = course_format_uses_sections($course->format);

// Print the list of instances (your module will probably extend this).

$timenow = time();
$strname = get_string("name");
$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_' . $course->format);
    $table->head = array($strsectionname, $strname);
} else {
    $table->head = array($strname);
}

// TODO: Put this in a renderer.
foreach ($models as $model) {
    $attribs = array('class' => 'wavefront-view-link');

    $captiondiv = html_writer::tag('div', format_text($model->intro, FORMAT_MOODLE),
                                    array('class' => "wavefront-description-caption"));
    $link = html_writer::link(new moodle_url('/mod/wavefront/view.php',
                                array('id' => $model->coursemodule)), get_string('viewgallery', 'mod_wavefront'), $attribs);

    if ($usesections) {
        $table->data[] = array(get_section_name($course, $model->section), $captiondiv . $link);
    } else {
        $table->data[] = array($link);
    }
}

echo html_writer::table($table);

// Finish the page.
echo $OUTPUT->footer();


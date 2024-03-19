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
 * @package    filter_wavefront
 * @copyright  2022 Ian Wild {@link https://ianwild.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Given a shortcode of the format:
// [wavefront id="n"]
// ...return the HTML to embed the specified model.
//
// This version is based on original multilang filter by Gaetan Frenoy,
// rewritten by Eloy and skodak.

/**
 * Implementation of the Moodle filter API for the Wavefront model filter.
 *
 * @copyright  2022 Ian Wild {@link https://ianwild.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_wavefront extends moodle_text_filter {
    public function filter($text, array $options = array()) {
        global $PAGE, $DB;

        if (empty($text) or is_numeric($text)) {
            return $text;
        }

        // Do a quick check using stripos to avoid unnecessary work.
        if ((strpos($text, '[') === false) &&
            (!preg_match('/\[wavefront id=/i', $text))) {
                return $text;
        }

        // Get mod_wavefront renderer.
        $renderer = $PAGE->get_renderer('mod_wavefront');
        if (!isset($renderer)) {
            return $text;
        }

        $search = '/\[wavefront id=.*\]/';

        preg_match_all($search, $text, $matches, PREG_OFFSET_CAPTURE );

        // Work backwards so we don't alter shortcode position.
        for ($i = count($matches[0])-1; $i >= 0; $i--) {
            // Parse id number from match.
            preg_match('!\d+!', $matches[0][$i][0], $id);
            if (isset($id[0])) {
                if ($model = $DB->get_record('wavefront_model', array('id' => $id[0]))) {
                    // Create a unique stage name, which will need to be passed to JS.
                    $stagename = uniqid('wavefront_');
                    list($course, $cm) = get_course_and_cm_from_instance($model->wavefrontid, 'wavefront');
                    $context = context_module::instance($cm->id);
                    $modelhtml = $renderer->display_model($context, $model, $stagename, false);
                    // Remove shortcode.
                    $text = substr_replace($text, '', $matches[0][$i][1], strlen($matches[0][$i][0]));
                    // Insert model.
                    $text = substr_replace($text, $modelhtml, $matches[0][$i][1], 0);
                }
            }
        }

        return $text;
    }
}

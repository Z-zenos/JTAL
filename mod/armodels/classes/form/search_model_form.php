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
 * Library of interface functions and constants.
 *
 * @package     mod_armodels
 * @copyright   2023 Hoang Anh Tuan <hoanganhtuanbk2001@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_armodels\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class search_model_form extends \moodleform {
    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'search', get_string('searchplaceholder', 'mod_armodels'), ['style' => 'display: inline; width: 100%', 'class' => 'mt-3']); // Add elements to your form.
        $mform->setType('search', PARAM_TEXT); // Set type of element.

        $submitlabel = get_string('search');
        $mform->addElement('submit', 'submitsearch', $submitlabel, ['style' => 'display: inline; width: 100px;']);
    }
}
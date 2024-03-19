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
 * The main Wavefront 3D model configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_lightboxgallery
 * @copyright 2011 John Kelsh <john.kelsh@netspot.com.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_wavefront_mod_form extends moodleform_mod {


    public function definition() {

        global $CFG;

        $mform =& $this->_form;

        // General options.

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size' => '48', 'maxlength' => '255'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        // Advanced options.

        $mform->addElement('header', 'wavefrontoptions', get_string('advanced'));

        $yesno = array(0 => get_string('no'), 1 => get_string('yes'));

        $mform->addElement('select', 'comments', get_string('allowcomments', 'wavefront'), $yesno);
        $mform->setType('comments', PARAM_INT);

        // Module options.
        $features = array('groups' => false, 'groupings' => false, 'groupmembersonly' => false,
                          'outcomes' => false, 'gradecat' => false, 'idnumber' => false);

        $this->standard_coursemodule_elements($features);

        $this->add_action_buttons();
    }

    /**
     * Add custom completion rules.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {

        $mform =& $this->_form;

        $group = array();
        $group[] =& $mform->createElement('checkbox', 'completioncommentsenabled', '',
                                            get_string('completioncomments', 'mod_wavefront'));
        $group[] =& $mform->createElement('text', 'completioncomments', '', array('size' => 3));
        $mform->setType('completioncomments', PARAM_INT);
        $mform->addGroup($group, 'completioncommentsgroup',
                            get_string('completioncommentsgroup', 'mod_wavefront'), array(' '), false);
        $mform->disabledIf('completioncomments', 'completioncommentsenabled', 'notchecked');

        return array('completioncommentsgroup');
    }

    /**
     * Called during validation
     *
     * {@inheritDoc}
     *
     * @see moodleform_mod::completion_rule_enabled()
     */
    public function completion_rule_enabled($data) {
        return (($data['comments'] == 1) && ($data['completioncommentsenabled'] != 0) && $data['completioncomments'] != 0);
    }

    /**
     * Set up the completion checkboxes when the form is displayed
     *
     * {@inheritDoc}
     *
     * @see moodleform_mod::data_preprocessing()
     */
    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        // Tick by default if Add mode or if completion comments settings is set to 1 or more.
        if (empty($this->_instance) || ($defaultvalues['comments'] == 1)) {
            $defaultvalues['completioncommentsenabled'] = 1;
        } else {
            $defaultvalues['completioncommentsenabled'] = 0;
        }
        if (empty($defaultvalues['completioncomments'])) {
            $defaultvalues['completioncomments'] = 1;
        }
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        // Turn off completion settings if the checkboxes aren't ticked.
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completioncommentsenabled) || !$autocompletion) {
                $data->completioncomments = 0;
            }
        }
    }
}


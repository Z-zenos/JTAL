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
 * @package   mod_wavefront
 * @copyright 2017 Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class mod_wavefront_edit_model_form extends moodleform {

    public function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $model              = $this->_customdata['model'];
        $cm                 = $this->_customdata['cm'];
        $descriptionoptions = $this->_customdata['descriptionoptions'];
        $modeloptions       = $this->_customdata['modeloptions'];

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('editor', 'description_editor', get_string('modeldescription', 'wavefront'), null, $descriptionoptions);
        $mform->setType('description_editor', PARAM_RAW);
        $mform->addRule('description_editor', get_string('required'), 'required', null, 'client');

        $descriptionposopts = array(
                WAVEFRONT_CAPTION_BOTTOM => get_string('position_bottom', 'wavefront'),
                WAVEFRONT_CAPTION_TOP => get_string('position_top', 'wavefront'),
                WAVEFRONT_CAPTION_HIDE => get_string('hide'),
        );
        $mform->addElement('select', 'descriptionpos', get_string('descriptionpos', 'wavefront'), $descriptionposopts);

        $mform->addElement('filemanager', 'model_filemanager', get_string('modelfiles', 'wavefront'), null, $modeloptions);
        $mform->addHelpButton('model_filemanager', 'modelfiles', 'wavefront');

        // Model type.
        $options = array(WAVEFRONT_MODEL_WAVEFRONT => get_string('wavefront_type', 'wavefront'),
                         WAVEFRONT_MODEL_COLLADA => get_string('collada_type', 'wavefront'));

        $mform->addElement('select', 'type', get_string('modeltype', 'wavefront'), $options);
        $mform->addHelpButton('type', 'modeltype', 'wavefront');

        // Stage.
        $mform->addElement('header', 'stageoptions', get_string('stageheading', 'wavefront'));

        $mform->addElement('text', 'stagewidth', get_string('stagewidth', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('stagewidth', 400);
        $mform->setType('stagewidth', PARAM_INT);

        $mform->addElement('text', 'stageheight', get_string('stageheight', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('stageheight', 400);
        $mform->setType('stageheight', PARAM_INT);

        $mform->addElement('text', 'backcol', get_string('backcol', 'wavefront'), 'maxlength="7" size="7"');
        $mform->addHelpButton('backcol', 'backcol', 'mod_wavefront');
        $mform->setDefault('backcol', '000000');
        $mform->setType('backcol', PARAM_ALPHANUM);

        // Camera.
        $mform->addElement('header', 'cameraoptions', get_string('cameraheading', 'wavefront'));

        $mform->addElement('text', 'cameraangle', get_string('cameraangle', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('cameraangle', 45);
        $mform->setType('cameraangle', PARAM_INT);

        $mform->addElement('text', 'cameranear', get_string('cameranear', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('cameranear', 1.0);
        $mform->setType('cameranear', PARAM_LOCALISEDFLOAT);

        $mform->addElement('text', 'camerafar', get_string('camerafar', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('camerafar', 1000);
        $mform->setType('camerafar', PARAM_INT);

        $mform->addElement('text', 'camerax', get_string('camerax', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('camerax', 0);
        $mform->setType('camerax', PARAM_INT);

        $mform->addElement('text', 'cameray', get_string('cameray', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('cameray', 1);
        $mform->setType('cameray', PARAM_INT);

        $mform->addElement('text', 'cameraz', get_string('cameraz', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('cameraz', 200);
        $mform->setType('cameraz', PARAM_INT);

        // Controls.
        $mform->addElement('header', 'controloptions', get_string('controlsheading', 'wavefront'));

        $mform->addElement('text', 'controlx', get_string('controlx', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('controlx', 0);
        $mform->setType('controlx', PARAM_INT);

        $mform->addElement('text', 'controly', get_string('controly', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('controly', 0);
        $mform->setType('controly', PARAM_INT);

        $mform->addElement('text', 'controlz', get_string('controlz', 'wavefront'), 'maxlength="5" size="5"');
        $mform->setDefault('controlz', 0);
        $mform->setType('controlz', PARAM_INT);

        // AR.
        $mform->addElement('header', 'aroptions', get_string('arheading', 'wavefront'));

        $mform->addElement('selectyesno', 'arenabled', get_string('arenabled', 'wavefront'));
        $mform->setDefault('arenabled', 0);
        $mform->addHelpButton('arenabled', 'arenabled', 'mod_wavefront');
        $mform->setType('selectyesno', PARAM_BOOL);

        $mform->addElement('text', 'arscale', get_string('arscale', 'wavefront'));
        $mform->addHelpButton('arscale', 'arscale', 'mod_wavefront');
        $mform->setDefault('arscale', 0.01);
        $mform->setType('arscale', PARAM_FLOAT);
        $mform->disabledIf('arscale', 'arenabled', 'eq', 0);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

        // -------------------------------------------------------------------------------
        $this->add_action_buttons();

        // -------------------------------------------------------------------------------
        $this->set_data($model);
    }

    public function validation($data, $files) {

        $errors = parent::validation($data, $files);

        // Ensure backcol is hexadecimal.
        if (!(ctype_xdigit($data['backcol'])
            && (strlen($data['backcol']) == 6 || strlen($data['backcol']) == 3))
        ) {

            $errors['backcol'] = get_string('backcolerr', 'mod_wavefront');
        }

        // Ensure AR object scaling > 0.
        if (($data['arenabled'] == 1) && ($data['arscale'] <= 0) ) {
            $errors['arscale'] = get_string('arscaleerr', 'mod_wavefront');
        }

        return $errors;
    }
}


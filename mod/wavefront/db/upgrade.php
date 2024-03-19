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
 * This file keeps track of upgrades to the 3D model module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package   mod_wavefront
 * @copyright 2017 onward Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * xmldb_wavefront_upgrade
 *
 * @param  int $oldversion
 * @return bool
 */
function xmldb_wavefront_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022022700) {

        $table = new xmldb_table('wavefront_model');
        $field = new xmldb_field('backcol', XMLDB_TYPE_CHAR, '15', null, true, null, null, 'stageheight');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update backcol to black where it is currently null.
        $records = $DB->get_records('wavefront_model', array('backcol' => ''));
        foreach ($records as $record) {
            $record->backcol = '000000';
            $DB->update_record('wavefront_model', $record);
        }

        upgrade_mod_savepoint(true, 2022022700, 'wavefront');
    }

    if ($oldversion < 2022032003) {

        $table = new xmldb_table('wavefront_model');
        $field = new xmldb_field('arenabled', XMLDB_TYPE_INTEGER, '2', null, true, null, 0, 'model');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('arscale', XMLDB_TYPE_FLOAT, null, null, true, null, 1, 'arenabled');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2022032003, 'wavefront');
    }

    if ($oldversion < 2022032601) {

        $table = new xmldb_table('wavefront');
        $field = new xmldb_field('completioncomments', XMLDB_TYPE_INTEGER, '9', null, true, null, 0, 'introformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2022032601, 'wavefront');
    }

    if ($oldversion < 2022041003) {

        $table = new xmldb_table('wavefront_model');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '8', null, true, null, null, 'wavefrontid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update model type to 'obj' where it is currently null.
        $records = $DB->get_records('wavefront_model', array('type' => ''));
        foreach ($records as $record) {
            $record->type = 'obj';
            $DB->update_record('wavefront_model', $record);
        }

        upgrade_mod_savepoint(true, 2022041003, 'wavefront');
    }

    if ($oldversion < 2022042303) {

        $table = new xmldb_table('wavefront_model');
        $field = new xmldb_field('cameranear', XMLDB_TYPE_NUMBER, '10, 5', null, true, null, 0.1, 'cameraangle');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2022042303, 'wavefront');
    }

    if ($oldversion < 2022042304) {

        $table = new xmldb_table('wavefront_model');
        $field = new xmldb_field('controlx', XMLDB_TYPE_INTEGER, '4', null, true, null, 0, 'backcol');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('controly', XMLDB_TYPE_INTEGER, '4', null, true, null, 0, 'controlx');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('controlz', XMLDB_TYPE_INTEGER, '4', null, true, null, 0, 'controly');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2022042304, 'wavefront');
    }

    return true;
}

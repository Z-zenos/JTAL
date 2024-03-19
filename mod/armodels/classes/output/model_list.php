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

namespace mod_armodels\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

class model_list implements renderable, templatable {
    /** @var array $models Models array to pass data to a template. */
    private $models = null;

    public function __construct($models) {
        $this->models = $models;
        $this->models = array_map(function ($model) {
            $model["publishedAt"] = date_format(
                date_create(explode("T", $model["publishedAt"])[0]),
                "d M, Y"
            );
            return $model;
        }, $this->models);
    }

    /**
    * Export data to be used as the context for a mustache template.
    *
    * @return stdClass
    */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->models = $this->models;

        return $data;
    }
}
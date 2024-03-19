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
 * Output a model's config buttons.
 *
 * @package   mod_wavefront
 * @copyright 2022 Ian Wild <ianwild1972@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_wavefront\output;

use moodle_url;
use templatable;
use renderable;

/**
 * Output a model's config buttons.
 *
 * @package   mod_wavefront
 * @copyright 2022 Ian Wild <ianwild1972@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class model_controls implements templatable, renderable {

    /**
     * @var object The context in which the model is to be rendered.
     */
    protected $context;

    /**
     * @var object The model we need to render.
     */
    protected $model;

    /**
     * @var bool True if editing is enabled, else false.
     */
    protected $editing;

    /**
     * @var int cmid.
     */
    protected $cmid;

    /**
     * Constructor for this object.
     */
    public function __construct(\context_module $context, $model, bool $editing, $cmid) {
        $this->context = $context;
        $this->model = $model;
        $this->editing = $editing;
        $this->cmid = $cmid;
    }

    /**
     * Data for use with a template.
     *
     * @param  \renderer_base $output render base output.
     * @return array Said data.
     */
    public function export_for_template(\renderer_base $output): array {
        $data = [];

        $data['editing'] = $this->editing;
        $data['editurl'] = new moodle_url('/mod/wavefront/edit_model.php');
        $data['id'] = $this->model->id;
        $data['wavefrontid'] = $this->model->wavefrontid;
        $data['cmid'] = $this->cmid;

        $data['candelete'] = has_capability('mod/wavefront:delete', $this->context);
        $data['delurl'] = new moodle_url('/mod/wavefront/delete_model.php');
        $data['arenabled'] = $this->model->arenabled;
        $data['arurl'] = new moodle_url('/mod/wavefront/ar.php');
        $filters = \core_plugin_manager::instance()->get_enabled_plugins('filter');
        $filterenabled = isset($filters['wavefront']);
        $data['canembed'] = has_capability('mod/wavefront:embed', $this->context) && $filterenabled;

        return $data;
    }
}

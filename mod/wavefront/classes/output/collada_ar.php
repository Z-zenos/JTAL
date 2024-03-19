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
 * Output a collada model in AR.
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
 * Output a collada model in AR.
 *
 * @package   mod_wavefront
 * @copyright 2022 Ian Wild <ianwild1972@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collada_ar implements templatable, renderable {



    /**
     * @var object The context in which the model is to be rendered.
     */
    protected $context;

    /**
     * @var object The model we need to render.
     */
    protected $model;

    /**
     * @var string The id of the DIV where three.js will add its canvas.
     */
    protected $stagename;

    /**
     * Constructor for this object.
     */
    public function __construct(\context_module $context, $model, $stagename) {
        $this->context = $context;
        $this->model = $model;
        $this->stagename = $stagename;
    }

    /**
     * Data for use with a template.
     *
     * @param  \renderer_base $output render base output.
     * @return array Said data.
     */
    public function export_for_template(\renderer_base $output): array {

        $data = [];

        $fs = get_file_storage();
        $fsfiles = $fs->get_area_files($this->context->id, 'mod_wavefront', 'model',
                                       $this->model->id, "itemid, filepath, filename", false);

        // A collada model contains an XML file. The textures are loaded automatically so we don't need to specify them.
        $modelerr = true;
        $daefile = null;

        foreach ($fsfiles as $f) {
            $pathname = $f->get_filepath();
            $filename = $f->get_filename();
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            // What type of file is this?
            if ($ext === "dae") {
                $daefile = moodle_url::make_pluginfile_url($this->context->id, 'mod_wavefront',
                                                           'model', $this->model->id, $pathname, $filename);
                $baseurl = moodle_url::make_pluginfile_url($this->context->id, 'mod_wavefront',
                                                           'model', $this->model->id, $pathname, '');
            }
        }

        if ($daefile != null) {
            $modelerr = false;
        }

        if ($this->model && !$modelerr) {
            $data['baseurl'] = urlencode($baseurl);
            $data['daefile'] = urlencode($daefile);
            $data['stagewidth'] = $this->model->stagewidth;
            $data['stageheight'] = $this->model->stageheight;
            $data['cameraangle'] = $this->model->cameraangle;
            $data['cameranear'] = $this->model->cameranear;
            $data['camerafar'] = $this->model->camerafar;
            $data['camerax'] = $this->model->camerax;
            $data['cameray'] = $this->model->cameray;
            $data['cameraz'] = $this->model->cameraz;
            $data['controlx'] = $this->model->controlx;
            $data['controly'] = $this->model->controly;
            $data['controlz'] = $this->model->controlz;
            $data['stagename'] = $this->stagename;
            $data['arscale'] = $this->model->arscale;
        }
        return $data;
    }
}

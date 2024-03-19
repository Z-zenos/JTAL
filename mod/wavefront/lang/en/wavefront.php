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
 * English strings for the wavefront 3D model renderer module
 *
 * @package   mod_wavefront
 * @copyright 2017 onward Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['wavefront:addinstance'] = 'Can add a new wavefront activity.';
$string['wavefront:viewcomments'] = 'View model gallery comments';
$string['wavefront:edit'] = 'Edit a model';
$string['wavefront:delete'] = 'Delete a model';
$string['wavefront:submit'] = 'Submit a model to the gallery';
$string['wavefront:addcomment'] = 'Comment on a gallery';
$string['wavefront:embed'] = 'Can embed a model (filter_wavefront plugin required)';

$string['acceptablefiletypebriefing'] = 'If you wish to upload multiple files at a time, you can submit a zip file with images inside it and all valid images inside the zip archive will be added to the gallery.';
$string['addcomment'] = 'Add comment';
$string['addmodel'] = 'Add model';
$string['addmodel_help'] = 'Browse for model files on your local machine to add to the current gallery.

You can also select a zip archive containing model files, which will be extracted into the model directory after being uploaded.';

$string['allowcomments'] = 'Allow comments';
$string['descriptionpos'] = 'Caption Position';
$string['commentadded'] = 'Your comment has been posted to the gallery';
$string['commentcount'] = '{$a} comments';
$string['commentdelete'] = 'Confirm comment deletion?';

$string['displayingmodel'] = '{$a}';

$string['errornofile'] = 'The requested file was not found: {$a}';
$string['errornowavefrontfiles'] = 'No Wavefront files found';
$string['errornocolladafiles'] = 'No Collada files found';
$string['erroruploadmodel'] = 'The files you upload must be a valid Wavefront model';
$string['eventmodelcommentcreated'] = 'Comment created';
$string['eventmodeladded'] = 'Wavefront model added';
$string['eventmodelupdated'] = 'Wavefront model updated';
$string['eventmodeldeleted'] = 'Wavefront model deleted';
$string['eventviewed'] = 'Wavefront model viewed';

$string['invalidwavefrontid'] = 'Invalid wavefront ID';
$string['wavefrontrenderer'] = 'Wavefront Renderer';

$string['modulename'] = 'Wavefront Renderer';
$string['modulename_help'] = 'The Wavefront 3D Renderer resource module enables participants to view a Wavefront .OBJ file format compliant 3D model.

This resource allows you to display \'3D\' images within your Moodle course.

As a course teacher, you are able to add and delete models.

If enabled, users are able to leave comments on your model.';
$string['modulenameplural'] = 'Wavefront Renderers';
$string['modulenameshort'] = 'Wavefront';
$string['modulenameadd'] = 'Wavefront Renderer';
$string['newmodelcomments'] = 'New model comments';
$string['nocomments'] = 'No comments';
$string['position_bottom'] = 'Bottom';
$string['position_top'] = 'Top';
$string['pluginadministration'] = 'Wavefront Renderer administration';
$string['pluginname'] = 'Wavefront Renderer';

// Model edit form.
$string['modeldescription'] = 'Description';
$string['modelfiles'] = 'Model files';
$string['modelfiles_help'] = 'Either upload all model files individually or you can select a zip archive containing the model files, which will be extracted into the model directory after being uploaded.';
$string['editmodel'] = 'Edit';
$string['stageheading'] = 'Stage';
$string['stagewidth'] = 'Width';
$string['stageheight'] = 'Height';
$string['backcol'] = 'Background colour';
$string['backcol_help'] = 'Specify the background colour as a hexadecimal triplet in RRGGBB format, e.g. FFFFFF for white.';
$string['backcolerr'] = 'Not a valid hexadecimal triplet';
$string['cameraheading'] = 'Camera';
$string['camerax'] = 'X';
$string['cameray'] = 'Y';
$string['cameraz'] = 'Z';
$string['cameraangle'] = 'View angle';
$string['cameranear'] = 'Near';
$string['camerafar'] = 'Far';
$string['controlsheading'] = 'Controls';
$string['controlx'] = 'X';
$string['controly'] = 'Y';
$string['controlz'] = 'Z';
$string['modeltype'] = 'Model file format';
$string['modeltype_help'] = 'Use this setting to specify the type of model you are uploading.';
$string['wavefront_type'] = 'Wavefront (.OBJ)';
$string['collada_type'] = 'Collada (.DAE)';

// Model management.
$string['deletemodel'] = 'Delete model';
$string['addmodel'] = 'Add new model';
$string['model'] = 'Model';
$string['deletemodelcheck'] = 'Are you sure you want to delete this model?';
$string['deletedmodel'] = 'Deleted model';
$string['deletemodelfiles'] = 'Deleting files...';
$string['deletemodelrecord'] = 'Delete record...';

// Course list of galleries.
$string['viewgallery'] = 'View gallery';

// Augmented Reality.
$string['arview'] = 'View in AR';
$string['arheading'] = 'Augmented Reality (AR)';
$string['arenabled'] = 'Enable AR view';
$string['arenabled_help'] = 'If \'Yes\' then an option to allow the viewer to open an AR tab will be shown beneath the model. Note AR will only work on WebXR-enabled devices.';
$string['arscale'] = 'Object scaling';
$string['arscale_help'] = 'Apply x y z vector scaling to the object.';
$string['arscaleerr'] = 'Object scaling should be greater than 0';

// Completion settings.
$string['completioncomments'] = 'Student must add comments to the gallery:';
$string['completioncommentsdesc'] = 'Student must add at least {$a} comment(s)';
$string['completioncommentsgroup'] = 'Require comments';
$string['completioncommentshelp'] = 'requiring comments to complete';

// Errors.
$string['commentsdisabled'] = 'Comments disabled';
$string['commentcreationfailed'] = 'Comment creation failed';
$string['invalidmodelid'] = 'Invalid model id';

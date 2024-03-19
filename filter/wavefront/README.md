# moodle-filter_wavefront
filter_wavefront is a Moodle plugin that allows content creators to easily include 3D models from a mod_wavefront gallery anywhere in Moodle that supports filtering. Please note this plugin requires version 2022061104 or above of the mod_wavefront activity - see <a href="https://github.com/iandavidwild/moodle-mod_wavefront">https://github.com/iandavidwild/moodle-mod_wavefront</a>.

The mod_wavefront and filter_wavefront plugins work together as follows:
* mod_wavefront generates unique shortcodes for each 3D model displayed in a gallery.
* filter_wavefront replaces a short code with the associated 3D model.

## Moodle
Moodle is a virtual learning environment (VLE) or course management system (CMS) - a free open source software package designed to help educators create effective online courses based on sound pedagogical principles. You can read more about Moodle by visiting <a href="https://moodle.org">https://moodle.org</a>

# Installing the plugin
This module can be installed as a ZIP file from the _Install plugins_ page. There are no additional steps required.

Note: if you want to install the plugin as a ZIP file, Moodle expects a single folder in the root named _wavefront_ (i.e. not the repo name moodle-mod_wavefront). If you want to download the source to a Moodle server you should copy it to /path/to/moodle/filter/wavefront.

## Dependences
filter_wavefront depends on mod_wavefront version 2022061104 (release 0.0.2.0) or above. Please visit <a href="https://github.com/iandavidwild/moodle-mod_wavefront">https://github.com/iandavidwild/moodle-mod_wavefront</a> for details. You will need to be viewing 3D models on a device capabable of running [WebGL](https://en.wikipedia.org/wiki/WebGL). WebGL is widely supported by modern browsers. However its availability can be dependent on other factors like the GPU supporting it.


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
 * Moodle Mini CMS block
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mcms\output;
global $CFG;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;

require_once($CFG->dirroot . '/blocks/mcms/lib.php');

/**
 * Class containing data for my mcms block.
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class layout_generic implements renderable, templatable {

    /**
     * @var string title
     */
    public $title;

    /**
     * @var string descriptionhtml
     */

    public $descriptionhtml;

    /**
     * @var string iconimageurl
     */

    public $iconimageurl;

    /**
     * @var string backgroundimageurl
     */

    public $backgroundimageurl;

    /**
     * @var string $backgroundcolor
     */
    public $backgroundcolor;

    /**
     * Main constructor.
     * Initialize the layout with current block config values
     *
     * @param stdClass $blockconfig
     *
     * @throws \dml_exception
     */
    public function __construct($blockconfig, $blockcontextid) {
        global $CFG;
        $this->title = $blockconfig->title;
        $this->descriptionhtml = format_text($blockconfig->text, $blockconfig->format);
        $fs = get_file_storage();
        $allfiles = $fs->get_area_files($blockcontextid, 'block_mcms', 'images');

        foreach ($allfiles as $file) {
            /* @var \stored_file $file */
            if ($this->is_valid_image($file)) {
                $filename = pathinfo($file->get_filename())['filename'];
                if ($filename == 'icon') {
                    $this->iconimageurl = \moodle_url::make_pluginfile_url(
                        $blockcontextid,
                        'block_mcms',
                        'images',
                        null,
                        $file->get_filepath(),
                        $file->get_filename()
                    )->out();
                }
                if ($filename == 'background') {
                    $this->backgroundimageurl = \moodle_url::make_pluginfile_url(
                        $blockcontextid,
                        'block_mcms',
                        'images',
                        null,
                        $file->get_filepath(),
                        $file->get_filename()
                    )->out();
                }
            }
        }
        $this->backgroundcolor = $blockconfig->backgroundcolor;

    }

    /**
     * The equivalent function does not work with svg
     *
     *
     * @param \stored_file $file
     * @return bool
     */
    protected function is_valid_image(\stored_file $file) {
        $mimetype = $file->get_mimetype();
        if (!file_mimetype_in_typegroup($mimetype, 'web_image')) {
            return false;
        }
        return true;
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array Context variables for the template
     * @throws \coding_exception
     *
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $USER;
        return get_object_vars($this); // All properties.
    }
}

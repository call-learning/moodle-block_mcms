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
 * Class containing data for the third type of layout (side image and text)
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class layout_three extends layout_generic {
    /**
     * @var string backgroundimageurl
     */

    public $sideimageurl;

    /**
     * Main constructor.
     * Initialize the layout with current block config values
     *
     * @param stdClass $blockconfig
     *
     * @throws \dml_exception
     */
    public function __construct($blockconfig, $blockcontextid) {
        parent::__construct($blockconfig, $blockcontextid);
    }

    protected function process_image($file, $blockcontextid) {
        parent::process_image($file, $blockcontextid);
        $filename = pathinfo($file->get_filename())['filename'];
        if ($filename == 'side-image') {
            $this->sideimageurl = \moodle_url::make_pluginfile_url(
                $blockcontextid,
                'block_mcms',
                'images',
                null,
                $file->get_filepath(),
                $file->get_filename()
            )->out();
        }
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
        return parent::export_for_template($output);
    }
}

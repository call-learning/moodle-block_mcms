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
use plugin_renderer_base;

defined('MOODLE_INTERNAL') || die;

/**
 * block mcms block renderer
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Return the main content for the block for the given layout
     *
     * @param layout_generic $layout The given layout
     * @return string HTML string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function render_main(layout_generic $layout) {
        $layoutclass = get_class($layout);
        return $this->render_from_template('block_mcms/' . $layoutclass,
            $layout->export_for_template($this));
    }
}

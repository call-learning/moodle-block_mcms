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
defined('MOODLE_INTERNAL') || die();

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
     * Process image
     *
     * @param object $file
     * @param string[] $filetypes
     */
    protected function process_image($file, $filetypes = array('icon', 'background')) {
        if (!in_array('side-image', $filetypes)) {
            $filetypes[] = 'side-image';
        }
        parent::process_image($file, $filetypes);
    }
}

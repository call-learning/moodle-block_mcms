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
 * Specialised backup task for the html block
 *
 * Requires encode_content_links in some configdata attrs.
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_mcms_block_task extends backup_block_task {

    /**
     * Encoded content link
     *
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        return $content; // No special encoding of links.
    }

    /**
     * Files areat to backup
     *
     * @return string[]
     */
    public function get_fileareas() {
        return ['content', 'images'];
    }

    /**
     * Encoded attributes
     *
     * @return string[]
     */
    public function get_configdata_encoded_attributes() {
        return ['text']; // We need to encode some attrs in configdata.
    }

    /**
     * Nothing to do here
     */
    protected function define_my_settings() {
    }

    /**
     * Nothing to do here
     */
    protected function define_my_steps() {
    }
}


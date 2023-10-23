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
 * Restore procedure for block MCMS
 *
 * @package   block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Specialised restore task for the mcms block
 *
 * (requires encode_content_links in some configdata attrs)
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_mcms_block_task extends restore_block_task {

    /**
     * Encode content
     *
     * @return array|void
     */
    public static function define_decode_contents() {
        $contents = [];
        $contents[] = new restore_mcms_block_decode_content('block_instances', 'configdata', 'block_instance');
        return $contents;
    }

    /**
     * Decode rule
     *
     * @return array|void
     */
    public static function define_decode_rules() {
        return [];
    }

    /**
     * Get all files areas
     *
     * @return string[]
     */
    public function get_fileareas() {
        return ['content', 'images'];
    }

    /**
     * Attributes to encode
     *
     * @return string[]
     */
    public function get_configdata_encoded_attributes() {
        return ['text']; // We need to encode some attrs in configdata.
    }

    /**
     * Nothing here
     */
    protected function define_my_settings() {
    }

    /**
     * Nothing here
     */
    protected function define_my_steps() {
    }
}

/**
 * Specialised restore_decode_content provider.
 *
 * Unserializes the configdata field, to serve the configdata->text
 * content to the restore_decode_processor packaging it back to its serialized form after process
 *
 * @package    block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_mcms_block_decode_content extends restore_decode_content {

    /**
     * @var mixed $configdata temp storage for unserialized configdata.
     */
    protected $configdata;

    /**
     * Iterator
     *
     * @return moodle_recordset
     * @throws dml_exception
     */
    protected function get_iterator() {
        global $DB;

        // Build the SQL dynamically here.
        $fieldslist = 't.' . implode(', t.', $this->fields);
        $sql = "SELECT t.id, $fieldslist
                  FROM {" . $this->tablename . "} t
                  JOIN {backup_ids_temp} b ON b.newitemid = t.id
                 WHERE b.backupid = ?
                   AND b.itemname = ?
                   AND t.blockname = 'mcms'";
        $params = [$this->restoreid, $this->mapping];
        return ($DB->get_recordset_sql($sql, $params));
    }

    /**
     * Preprocess  field
     *
     * @param string $field
     * @return string
     */
    protected function preprocess_field($field) {
        $this->configdata = unserialize(base64_decode($field));
        return isset($this->configdata->text) ? $this->configdata->text : '';
    }

    /**
     * Preprocess  field
     *
     * @param string $field
     * @return string
     */
    protected function postprocess_field($field) {
        $this->configdata->text = $field;
        return base64_encode(serialize($this->configdata));
    }
}

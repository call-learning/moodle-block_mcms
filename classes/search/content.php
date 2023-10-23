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
namespace block_mcms\search;

use context;
use context_system;
use core_search\base_block;
use core_search\document_factory;
use core_search\manager;

/**
 * Search area for block_mcms blocks
 *
 * @package block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends base_block {

    /**
     * Get relevant document
     *
     * @param \stdClass $record
     * @param array $options
     * @return \core_search\document
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_document($record, $options = []) {
        // Create empty document.
        $doc = document_factory::instance($record->id,
            $this->componentname, $this->areaname);

        // Get stdclass object with data from DB.
        $data = unserialize(base64_decode($record->configdata));

        // Get content.
        $content = content_to_text($data->text, $data->format);
        $doc->set('content', $content);

        if (isset($data->title)) {
            // If there is a title, use it as title.
            $doc->set('title', content_to_text($data->title, false));
        } else {
            // If there is no title, use the content text again.
            $doc->set('title', shorten_text($content));
        }

        // Set standard fields.
        $doc->set('contextid', \context_block::instance($record->id)->id);
        $doc->set('type', manager::TYPE_TEXT);
        $doc->set('courseid', SITEID);
        $doc->set('modified', $record->timemodified);
        $doc->set('owneruserid', manager::NO_OWNER_ID);

        // Mark document new if appropriate.
        if (isset($options['lastindexedtime']) &&
            ($options['lastindexedtime'] < $record->timecreated)) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Can use file indexing ?
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Attach files to document
     *
     * @param \core_search\document $document
     * @throws \coding_exception
     */
    public function attach_files($document) {
        $fs = get_file_storage();

        $context = context::instance_by_id($document->get('contextid'));

        $files = $fs->get_area_files($context->id, 'block_mcms', 'images');
        foreach ($files as $file) {
            $document->add_stored_file($file);
        }
        $files = $fs->get_area_files($context->id, 'block_mcms', 'content');
        foreach ($files as $file) {
            $document->add_stored_file($file);
        }
    }

    /**
     * Override the get_document recordset so we search in other context also
     *
     * The block mcms can be in global context (system context), on page pattern such
     * as mcmspage also.
     *
     * @param int $modifiedfrom Return only records modified after this date
     * @param \context|null $context Context to find blocks within
     * @return false|\moodle_recordset|null
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;

        // Get context restrictions.
        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql($context, 'bi');

        // Get custom restrictions for block type.
        list ($restrictions, $restrictionparams) = $this->get_indexing_restrictions();
        if ($restrictions) {
            $restrictions = 'AND ' . $restrictions;
        }

        // Query for all entries in block_instances for this type of block, within the specified
        // context. The query is based on the one from get_recordset_by_timestamp and applies the
        // same restrictions.
        return $DB->get_recordset_sql("
                SELECT bi.id, bi.timemodified, bi.timecreated, bi.configdata,
                       c.id AS courseid, x.id AS contextid
                  FROM {block_instances} bi
                       $contextjoin
                  JOIN {context} x ON x.instanceid = bi.id AND x.contextlevel = ?
                  JOIN {context} parent ON parent.id = bi.parentcontextid
             LEFT JOIN {course_modules} cm ON cm.id = parent.instanceid AND parent.contextlevel = ?
                  JOIN {course} c ON c.id = cm.course
                       OR (c.id = parent.instanceid AND parent.contextlevel = ?)
                 WHERE bi.timemodified >= ?
                       AND bi.blockname = ?
                       AND (" . $DB->sql_like('bi.pagetypepattern', '?') . "
                           OR bi.pagetypepattern IN ('site-index', 'mcms', 'course-*', '*'))
                       $restrictions
              ORDER BY bi.timemodified ASC",
            array_merge($contextparams, [CONTEXT_BLOCK, CONTEXT_MODULE, CONTEXT_COURSE,
                $modifiedfrom, $this->get_block_name(), '%', ],
                $restrictionparams));
    }

    /**
     * Override so we sort out the new page type (mcms)
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function get_doc_url(\core_search\document $doc) {
        // Load block instance and find cmid if there is one.
        $blockinstanceid = preg_replace('~^.*-~', '', $doc->get('id'));
        $instance = $this->get_block_instance($blockinstanceid);
        $courseid = $doc->get('courseid');
        $anchor = 'inst' . $blockinstanceid;

        // Check if the block is at course or module level.
        if ($instance->cmid) {
            // No module-level page types are supported at present so the search system won't return
            // them. But let's put some example code here to indicate how it could work.
            debugging('Unexpected module-level page type for block ' . $blockinstanceid . ': ' .
                $instance->pagetypepattern, DEBUG_DEVELOPER);
            $modinfo = get_fast_modinfo($courseid);
            $cm = $modinfo->get_cm($instance->cmid);
            return new \moodle_url($cm->url, null, $anchor);
        } else {
            // The block is at course level. Let's check the page type, although in practice we
            // currently only support the course main page.
            if ($instance->pagetypepattern === '*' || $instance->pagetypepattern === 'course-*' ||
                preg_match('~^course-view-(.*)$~', $instance->pagetypepattern)) {
                return new \moodle_url('/course/view.php', ['id' => $courseid], $anchor);
            } else if ($instance->pagetypepattern === 'site-index') {
                return new \moodle_url('/', ['redirect' => 0], $anchor);
            } else if ($instance->pagetypepattern === 'mcms') {
                $pageinstanceid = empty($instance->subpagepattern) ? 0 : $instance->subpagepattern;
                return new \moodle_url('/local/mcms/index.php', ['id' => $pageinstanceid], $anchor);
            } else {
                debugging('Unexpected page type for block ' . $blockinstanceid . ': ' .
                    $instance->pagetypepattern, DEBUG_DEVELOPER);
                return new \moodle_url('/course/view.php', ['id' => $courseid], $anchor);
            }
        }
    }
}

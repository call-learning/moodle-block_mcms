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
 * Class for MCMS block search tests
 *
 * @package   block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once('block_mcms_test_base.php');

/**
 * Unit test for search indexing.
 *
 * @package   block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_content_test extends advanced_testcase {
    use block_mcms_test_base;

    /**
     * Tests all functionality in the search area.
     */
    public function test_search_area() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
        $this->setAdminUser();

        $before = time();
        $block = $this->setup_block(self::LAYOUT_ONE_CONFIG);

        // Set up fake search engine so we can create documents.
        \testable_core_search::instance();

        // Do indexing query.
        $area = new \block_mcms\search\content();
        $this->assertEquals('mcms', $area->get_block_name());
        $rs = $area->get_recordset_by_timestamp();
        $count = 0;
        $course = $DB->get_record('course', array('id' => SITEID)); // Frontpage.
        $after = time();
        foreach ($rs as $record) {
            $count++;

            $this->assertEquals($course->id, $record->courseid);

            // Check context is correct.
            $blockcontext = \context::instance_by_id($record->contextid);
            $this->assertInstanceOf('\context_block', $blockcontext);
            $coursecontext = $blockcontext->get_parent_context();
            $this->assertEquals($course->id, $coursecontext->instanceid);

            // Check created and modified times are correct.
            $this->assertTrue($record->timecreated >= $before && $record->timecreated <= $after);
            $this->assertTrue($record->timemodified >= $before && $record->timemodified <= $after);

            // Get config data.
            $data = unserialize(base64_decode($record->configdata));
            $this->assertEquals('block title', $data->title);
            $this->assertContains('Lorem ipsum', $data->text);
            $this->assertEquals(FORMAT_HTML, $data->format);

            // Check the get_document function 'new' flag.
            $doc = $area->get_document($record, ['lastindexedtime' => 1]);
            $this->assertTrue($doc->get_is_new());
            $doc = $area->get_document($record, ['lastindexedtime' => time() + 1]);
            $this->assertFalse($doc->get_is_new());

            // Check the attach_files function results in correct list of associated files.
            $this->assertCount(0, $doc->get_files());
            $area->attach_files($doc);
            $files = $doc->get_files();
            $this->assertCount(6, $files);
            $filenamewdir = array_map(
                function($file) {
                    return $file->get_filename();
                },
                array_filter($files, function($f) {
                    return !$f->is_directory();
                }));
            $this->assertEquals(array(
                'background.jpg',
                'icon.jpg',
                'other-image.jpg',
                'text-image.jpg',
            ), array_values($filenamewdir));

            // Check the document fields are all as expected.
            $this->assertEquals('block title', $doc->get('title'));
            $this->assertContains('Lorem ipsum', $doc->get('content'));
            $this->assertEquals($blockcontext->id, $doc->get('contextid'));
            $this->assertEquals(\core_search\manager::TYPE_TEXT, $doc->get('type'));
            $this->assertEquals($course->id, $doc->get('courseid'));
            $this->assertEquals($record->timemodified, $doc->get('modified'));
            $this->assertEquals(\core_search\manager::NO_OWNER_ID, $doc->get('owneruserid'));

            // Also check getting the doc url and context url.
            $url = new \moodle_url('/', ['redirect' => 0], 'inst' . $record->id);
            $this->assertTrue($url->compare($area->get_doc_url($doc)));
            $this->assertTrue($url->compare($area->get_context_url($doc)));
        }
        $rs->close();

        // Should only be one MCMS block systemwide.
        $this->assertEquals(1, $count);

        // If we run the query starting from 1 second after now, there should be no results.
        $rs = $area->get_recordset_by_timestamp($after + 1);
        $count = 0;
        foreach ($rs as $record) {
            $count++;
        }
        $rs->close();
        $this->assertEquals(0, $count);
    }

    // @codingStandardsIgnoreStart
    // phpcs:disable
    /**
     * First layout
     */
    const LAYOUT_ONE_CONFIG = [
        'title' => 'block title',
        'backgroundcolor' => '#fefefe',
        'classes' => 'my-class-name',
        'decorations' => 'mcms-square',
        'layout' => 'layout_one',
        'contentimages' => array(
            'text-image.jpg',
            'other-image.jpg'),
        'images' => array('other-image.jpg' => 'icon.jpg',
            'background.jpg' => 'background.jpg'),
    ];
    // phpcs:enable
    // @codingStandardsIgnoreEnd
}


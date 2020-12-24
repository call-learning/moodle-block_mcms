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
 * Unit tests for block_mcms backup process
 *
 * @package   block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_mcms\output\layout_four;
use block_mcms\output\layout_one;
use block_mcms\output\layout_three;
use block_mcms\output\layout_two;

defined('MOODLE_INTERNAL') || die();
require_once('block_mcms_test_base.php');
global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Unit tests for block_mcms backup process
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mcms_backup_test extends advanced_testcase {
    use block_mcms_test_base;
    /**
     * Test that output is as expected. This also test file loading into the plugin.
     *
     * See test_restore_frontpage (backup/moodle2/tests/moodle2_test.php)
     */
    public function test_backup_restore() {
        global $DB;
        $fs = get_file_storage();
        $this->resetAfterTest();
        $this->setAdminUser();
        $block = $this->setup_block(self::LAYOUT_ONE_CONFIG);
        $content = $block->get_content();
        $this->assertNotNull($content->text);
        $this->assertEquals(array(
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '3a094fa1830a652a7b4dc931ffe1961f042230e4',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ), array_values($this->get_block_file_hash($block)));

        $frontpage = $DB->get_record('course', array('id' => SITEID));
        // Create the initial backupcontoller.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $frontpage->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            2,
            \backup::RELEASESESSION_YES);
        $bc->execute_plan();
        $backupid = $bc->get_backupid();
        $bc->destroy();

        // Now change the block settings.
        // We replace an imag.
        $configdata = $block->config;
        $configdata->title = 'Block title'; // Change block title.
        unset($configdata->text);
        $configdata->backgroundcolor = '#fefefe'; // Change block background color.
        $contentimages = array(); // No more images.
        $images = array('other-image.jpg' => 'icon.jpg');
        $this->upload_files_in_block(
            $configdata,
            $contentimages,
            array_keys($images),
            array_values($images)
        );
        $block->instance_config_save($configdata);

        $block = block_instance_by_id($block->instance->id);
        $content = $block->get_content();
        $expected = self::LAYOUT_ONE_RENDER_RESULT_AMENDED;
        $this->assertEquals($expected, $this->filter_out_content($content->text, $block)); // This should not be equal anymore.
        $this->assertEquals(array(
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ), array_values($this->get_block_file_hash($block)));

        // Now restore.
        $rc = new \restore_controller($backupid,
            $frontpage->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            2,
            \backup::TARGET_CURRENT_DELETING);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        $blocks = $this->get_mcms_blocks_on_frontpage();
        $this->assertCount(1, $blocks);
        $block = end($blocks);
        $block = block_instance_by_id($block->instance->id);
        $content = $block->get_content();
        // Now we should be back to the original.
        $expected = self::LAYOUT_ONE_RENDER_RESULT;
        $this->assertEquals($expected, $this->filter_out_content($content->text, $block)); // This should be equal after restore.
        $this->assertEquals(array(
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '3a094fa1830a652a7b4dc931ffe1961f042230e4',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ), array_values($this->get_block_file_hash($block)));
    }

    /**
     * Get current blocks on frontpage
     *
     * @return array
     * @throws dml_exception
     */
    protected function get_mcms_blocks_on_frontpage() {
        $page = $this->create_front_page();
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        return array_filter($blocks, function($block) {
            return $block->instance->blockname == 'mcms';
        });
    }
    // @codingStandardsIgnoreStart
    // phpcs:disable
    /**
     * Layout render result
     */
    const LAYOUT_ONE_RENDER_RESULT_AMENDED = <<<EOT
<div  class="block-mcms block-cards container pt-5 pb-5">
    <div class="d-flex flex-row justify-content-center inner-block">

        <div style="background-image: url(https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/icon.jpg);" class="blockicon d-none d-sm-block"></div>

        <div class="text-content position-relative">
                <div class="mcms-square" style="position:absolute;"></div>
            <h3>Block title</h3>
            <div class="pb-5">
                <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit,sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum </p>
            </div>
        </div>
    </div>
</div>
EOT;
    /**
     * First layout
     */
    const LAYOUT_ONE_RENDER_RESULT = <<<EOT
<div  class="block-mcms block-cards container pt-5 pb-5">
    <div class="d-flex flex-row justify-content-center inner-block">

        <div style="background-image: url(https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/icon.jpg);" class="blockicon d-none d-sm-block"></div>

        <div class="text-content position-relative">
                <div class="mcms-square" style="position:absolute;"></div>
            <h3>block title</h3>
            <div class="pb-5">
                <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit,sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum </p><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg" alt="text-image.jpg" /><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" alt="other-image.jpg" />
            </div>
        </div>
    </div>
</div>
EOT;
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
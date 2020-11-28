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
 * Base class for MCMS block tests
 *
 * @package   block_mcms
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use block_mcms\output\layout_four;
use block_mcms\output\layout_one;
use block_mcms\output\layout_three;
use block_mcms\output\layout_two;
require_once('mcms_test_base.php');

/**
 * Unit tests for block_mcms
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mcms_test extends advanced_testcase {
    use mcms_test_base;

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     * @dataProvider layout_renderer_test_provider
     */
    public function test_render_layout($layoutconfig, $expectedstrings) {
        $block = $this->setup_block(self::LAYOUT_ONE_CONFIG);
        $content = $block->get_content();
        $this->assertNotNull($content->text);

        $expected = self::LAYOUT_ONE_RENDER_RESULT;
        $text = $this->filter_out_content($content->text, $block);
        $this->assertEquals($expected, $text);
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     */
    public function test_update_image() {
        $block = $this->setup_block(self::LAYOUT_ONE_CONFIG);
        $this->assertEquals(array(
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '3a094fa1830a652a7b4dc931ffe1961f042230e4',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ), array_values($this->get_block_file_hash($block)));
        // Now update the block images.
        $usercontext = context_user::instance($this->user->id);
        $draftitemid = file_get_unused_draft_itemid();
        $configdata = $block->config;
        $configdata->images = $draftitemid;
        $this->upload_image_draft_area($usercontext,
            $draftitemid,
            array('other-image.jpg'),
            array('icon.jpg'));
        $block->instance_config_save($configdata);
        $block = block_instance_by_id($block->instance->id);
        $this->assertEquals(array(
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ), array_values($this->get_block_file_hash($block)));
        // Now back to original.
        $configdata = $block->config;
        $configdata->images = $draftitemid;
        $this->upload_image_draft_area($usercontext,
            $draftitemid,
            array('background.jpg'),
            array('icon.jpg'));
        $block->instance_config_save($configdata);
        $block = block_instance_by_id($block->instance->id);
        $this->assertEquals(array(
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ), array_values($this->get_block_file_hash($block)));

    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     */
    public function test_output_renderer_layout_one() {
        $block = $this->setup_block(self::LAYOUT_ONE_CONFIG);
        $renderer = $block->page->get_renderer('core');
        $renderable = new layout_one($block->config, $block->context->id);
        $exported = $renderable->export_for_template($renderer);
        $this->assertEquals('#fefefe', $exported['backgroundcolor']);
        $this->assertEquals('block title', $exported['title']);
        $this->assertIsArray($exported['decorations']);
        $this->assertEquals(array('mcms-square'), $exported['decorations']);
        $this->assertStringEndsWith('icon.jpg', $exported['iconurl']);
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     */
    public function test_output_renderer_layout_two() {
        $block = $this->setup_block(self::LAYOUT_TWO_CONFIG);
        $renderer = $block->page->get_renderer('core');
        $renderable = new layout_two($block->config, $block->context->id);
        $exported = $renderable->export_for_template($renderer);
        $this->assertEquals('#fefefe', $exported['backgroundcolor']);
        $this->assertEquals('block title', $exported['title']);
        $this->assertIsArray($exported['decorations']);
        $this->assertEquals(array('mcms-square'), $exported['decorations']);
        $this->assertStringEndsWith('background.jpg', $exported['backgroundurl']);
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     */
    public function test_output_renderer_layout_three() {
        $block = $this->setup_block(self::LAYOUT_THREE_CONFIG);
        $block = block_instance_by_id($block->instance->id);
        $renderer = $block->page->get_renderer('core');
        $renderable = new layout_three($block->config, $block->context->id);
        $exported = $renderable->export_for_template($renderer);
        $this->assertEquals('#fefefe', $exported['backgroundcolor']);
        $this->assertEquals('block title', $exported['title']);
        $this->assertIsArray($exported['decorations']);
        $this->assertEquals(array('mcms-square'), $exported['decorations']);
        $this->assertStringEndsWith('side-image.jpg', $exported['sideimageurl']);
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     */
    public function test_output_renderer_layout_four() {
        $block = $this->setup_block(self::LAYOUT_FOUR_CONFIG);
        $block = block_instance_by_id($block->instance->id);
        $renderer = $block->page->get_renderer('core');
        $renderable = new layout_four($block->config, $block->context->id);
        $exported = $renderable->export_for_template($renderer);
        $this->assertEquals('#fefefe', $exported['backgroundcolor']);
        $this->assertEquals('block title', $exported['title']);
        $this->assertIsArray($exported['decorations']);
        $this->assertEquals(array('mcms-square'), $exported['decorations']);
        $this->assertStringEndsWith('side-image.jpg', $exported['sideimageurl']);
    }

    // @codingStandardsIgnoreStart
    // phpcs:disable

    /**
     * Data provider
     *
     * @return array[]
     */
    public function layout_renderer_test_provider() {
        return array(
            'Render layout 1' => array(
                'layoutconfig' => self::LAYOUT_ONE_CONFIG,
                'expectedstrings' => array(
                    'url(https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/icon.jpg)',
                    '<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit,sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum </p><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg" alt="text-image.jpg" /><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" alt="other-image.jpg" /></div>'
                )
            ),
            'Render layout 2' => array(
                'layoutconfig' => self::LAYOUT_TWO_CONFIG,
                'expectedstrings' => array(
                    'style="background-image: url(https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/background.jpg);',
                    '<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit,sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum </p><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg" alt="text-image.jpg" /><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" alt="other-image.jpg" /></div>'
                )
            ),
            'Render layout 3' => array(
                'layoutconfig' => self::LAYOUT_THREE_CONFIG,
                'expectedstrings' => array(
                    '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/side-image.jpg" class="side-image m-3 img-fluid d-none d-sm-flex"/>',
                    '<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit,sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum </p><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg" alt="text-image.jpg" /><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" alt="other-image.jpg" /></div>'
                )
            ),
            'Render layout 4' => array(
                'layoutconfig' => self::LAYOUT_FOUR_CONFIG,
                'expectedstrings' => array(
                    '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/side-image.jpg" class="side-image m-3 img-fluid d-none d-sm-flex"/>',
                    '<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit,sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum </p><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg" alt="text-image.jpg" /><img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" alt="other-image.jpg" /></div>'
                )
            )
        );
    }

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
    /**
     * Second layout
     */
    const LAYOUT_TWO_CONFIG = [
        'title' => 'block title',
        'backgroundcolor' => '#fefefe',
        'classes' => 'my-class-name',
        'decorations' => 'mcms-square',
        'layout' => 'layout_two',
        'contentimages' => array(
            'text-image.jpg',
            'other-image.jpg'),
        'images' => array('background.jpg' => 'background.jpg'),
    ];
    /**
     * Third layout
     */
    const LAYOUT_THREE_CONFIG = [
        'title' => 'block title',
        'backgroundcolor' => '#fefefe',
        'classes' => 'my-class-name',
        'decorations' => 'mcms-square',
        'layout' => 'layout_three',
        'contentimages' => array(
            'text-image.jpg',
            'other-image.jpg'),
        'images' => array('other-image.jpg' => 'side-image.jpg'),
    ];
    /**
     * Fourth layout
     */
    const LAYOUT_FOUR_CONFIG = [
        'title' => 'block title',
        'backgroundcolor' => '#fefefe',
        'classes' => 'my-class-name',
        'decorations' => 'mcms-square',
        'layout' => 'layout_four',
        'contentimages' => array(
            'text-image.jpg',
            'other-image.jpg'),
        'images' => array('other-image.jpg' => 'side-image.jpg'),
    ];
    // phpcs:enable
    // @codingStandardsIgnoreEnd

}

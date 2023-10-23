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
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mcms;
defined('MOODLE_INTERNAL') || die();
require_once('block_mcms_test_base.php');

use advanced_testcase;
use block_mcms\output\layout_four;
use block_mcms\output\layout_one;
use block_mcms\output\layout_three;
use block_mcms\output\layout_two;
use block_mcms_test_base;
use context_user;

/**
 * Unit tests for block_mcms
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mcms_test extends advanced_testcase {
    use block_mcms_test_base;

    /**
     * First layout
     */
    const LAYOUT_ONE_CONFIG = [
        'title' => 'block title',
        'backgroundcolor' => '#fefefe',
        'classes' => 'my-class-name',
        'decorations' => 'mcms-square',
        'layout' => 'layout_one',
        'contentimages' => [
            'text-image.jpg',
            'other-image.jpg',
        ],
        'images' => [
            'other-image.jpg' => 'icon.jpg',
            'background.jpg' => 'background.jpg',
        ],
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
        'contentimages' => [
            'text-image.jpg',
            'other-image.jpg',
        ],
        'images' => ['background.jpg' => 'background.jpg'],
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
        'contentimages' => [
            'text-image.jpg',
            'other-image.jpg',
        ],
        'images' => ['other-image.jpg' => 'side-image.jpg'],
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
        'contentimages' => [
            'text-image.jpg',
            'other-image.jpg',
        ],
        'images' => ['other-image.jpg' => 'side-image.jpg'],
    ];

    /**
     * Data provider
     *
     * @return array
     */
    public static function layout_renderer_test_provider(): array {
        return [
            'Render layout 1' => [
                'layoutconfig' => self::LAYOUT_ONE_CONFIG,
                'expectedstrings' => [
                    'url(https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/icon.jpg)',
                    'sunt in culpa qui officia deserunt mollit anim id est laborum </p>'
                    . '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg"'
                    . ' alt="text-image.jpg" />'
                    . '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" '
                    . 'alt="other-image.jpg" />',
                ],
            ],
            'Render layout 2' => [
                'layoutconfig' => self::LAYOUT_TWO_CONFIG,
                'expectedstrings' => [
                    'style="background-image:'
                    . ' url(https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/background.jpg);',
                    'sunt in culpa qui officia deserunt mollit anim id est laborum </p>'
                    . '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg"'
                    . ' alt="text-image.jpg" />'
                    . '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg"'
                    . ' alt="other-image.jpg" />',
                ],
            ],
            'Render layout 3' => [
                'layoutconfig' => self::LAYOUT_THREE_CONFIG,
                'expectedstrings' => [
                    '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/side-image.jpg" '
                    . 'class="side-image m-3 img-fluid d-none d-sm-flex" alt="Illustrative side image">',
                    'sunt in culpa qui officia deserunt mollit anim id est laborum </p>'
                    . '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg"'
                    . ' alt="text-image.jpg" /><img '
                    . 'src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" '
                    . 'alt="other-image.jpg" />',
                ],
            ],
            'Render layout 4' => [
                'layoutconfig' => self::LAYOUT_FOUR_CONFIG,
                'expectedstrings' => [
                    '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/images/side-image.jpg"'
                    . ' class="side-image m-3 img-fluid d-none d-sm-flex" alt="Illustrative side image">',
                    'sunt in culpa qui officia deserunt mollit anim id est laborum </p>'
                    . '<img src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/text-image.jpg"'
                    . ' alt="text-image.jpg" /><img '
                    . 'src="https://www.example.com/moodle/pluginfile.php/BLOCKCONTEXTID/block_mcms/content/other-image.jpg" '
                    . 'alt="other-image.jpg" />',
                ],
            ],
        ];
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     *
     * @dataProvider layout_renderer_test_provider
     * @param array $layoutconfig
     * @param array $expectedstrings
     * @covers       \block_mcms::get_content
     */
    public function test_render_layout($layoutconfig, $expectedstrings) {
        $block = $this->setup_block($layoutconfig);
        $content = $block->get_content();
        $this->assertNotNull($content->text);

        $text = $this->filter_out_content($content->text, $block);
        foreach ($expectedstrings as $expected) {
            $this->assertStringContainsString($expected, $text);
        }
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     *
     * @covers \block_mcms::get_content
     */
    public function test_update_image() {
        $block = $this->setup_block(self::LAYOUT_ONE_CONFIG);
        $this->assertEquals([
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '3a094fa1830a652a7b4dc931ffe1961f042230e4',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ], array_values($this->get_block_file_hash($block)));
        // Now update the block images.
        $usercontext = context_user::instance($this->user->id);
        $draftitemid = file_get_unused_draft_itemid();
        $configdata = $block->config;
        $configdata->images = $draftitemid;
        $this->upload_image_draft_area($usercontext,
            $draftitemid,
            ['other-image.jpg'],
            ['icon.jpg']);
        $block->instance_config_save($configdata);
        $block = block_instance_by_id($block->instance->id);
        $this->assertEquals([
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ], array_values($this->get_block_file_hash($block)));
        // Now back to original.
        $configdata = $block->config;
        $configdata->images = $draftitemid;
        $this->upload_image_draft_area($usercontext,
            $draftitemid,
            ['background.jpg'],
            ['icon.jpg']);
        $block->instance_config_save($configdata);
        $block = block_instance_by_id($block->instance->id);
        $this->assertEquals([
            'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            '012748f18059cb31f77a026ecd3665f43c146560',
        ], array_values($this->get_block_file_hash($block)));

    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     *
     * @covers \block_mcms\output\layout_one::export_for_template
     */
    public function test_output_renderer_layout_one() {
        $block = $this->setup_block(self::LAYOUT_ONE_CONFIG);
        $renderer = $block->page->get_renderer('core');
        $renderable = new layout_one($block->config, $block->context->id);
        $exported = $renderable->export_for_template($renderer);
        $this->assertEquals('#fefefe', $exported['backgroundcolor']);
        $this->assertEquals('block title', $exported['title']);
        $this->assertIsArray($exported['decorations']);
        $this->assertEquals(['mcms-square'], $exported['decorations']);
        $this->assertStringEndsWith('icon.jpg', $exported['iconurl']);
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     *
     * @covers \block_mcms\output\layout_two::export_for_template
     */
    public function test_output_renderer_layout_two() {
        $block = $this->setup_block(self::LAYOUT_TWO_CONFIG);
        $renderer = $block->page->get_renderer('core');
        $renderable = new layout_two($block->config, $block->context->id);
        $exported = $renderable->export_for_template($renderer);
        $this->assertEquals('#fefefe', $exported['backgroundcolor']);
        $this->assertEquals('block title', $exported['title']);
        $this->assertIsArray($exported['decorations']);
        $this->assertEquals(['mcms-square'], $exported['decorations']);
        $this->assertStringEndsWith('background.jpg', $exported['backgroundurl']);
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     *
     * @covers \block_mcms\output\layout_three::export_for_template
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
        $this->assertEquals(['mcms-square'], $exported['decorations']);
        $this->assertStringEndsWith('side-image.jpg', $exported['sideimageurl']);
    }

    /**
     * Test that output is as expected. This also test file loading into the plugin.
     *
     * @covers \block_mcms\output\layout_four::export_for_template
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
        $this->assertEquals(['mcms-square'], $exported['decorations']);
        $this->assertStringEndsWith('side-image.jpg', $exported['sideimageurl']);
    }
}

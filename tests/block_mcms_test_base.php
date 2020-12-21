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
defined('MOODLE_INTERNAL') || die();
/**
 * Base class for MCMS block tests
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait block_mcms_test_base {
    /**
     * Current block
     *
     * @var block_base|false|null
     */
    protected $block = null;
    /**
     * Current user
     *
     * @var stdClass|null
     */
    protected $user = null;

    // @codingStandardsIgnoreStart
    // phpcs:disable
    /**
     * Basic setup for these tests.
     */
    public function setUp() {
        $this->resetAfterTest(true);
        $this->user = $this->getDataGenerator()->create_user();
    }
    // phpcs:enable
    // @codingStandardsIgnoreEnd

    /**
     * Setup block
     *
     * @param object|array $configdata
     * @return block_base|false
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    protected function setup_block($configdata) {
        if (is_array($configdata)) {
            $configdata = (object) $configdata;
        }
        $this->setUser($this->user);
        // Create a Sponsor block.

        $page = $this->create_front_page();
        $blockname = 'mcms';
        $page->blocks->load_blocks();
        $page->blocks->add_block_at_end_of_default_region($blockname);
        // Here we need to work around the block API. In order to get 'get_blocks_for_region' to work,
        // we would need to reload the blocks (as it has been added to the DB but is not
        // taken into account in the block manager).
        // The only way to do it is to recreate a page so it will reload all the block.
        // It is a main flaw in the  API (not being able to use load_blocks twice).
        // Alternatively if birecordsbyregion was nullable,
        // should for example have a load_block + create_all_block_instances and
        // should be able to access to the block.
        $page = $this->create_front_page();
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        $block = block_instance($blockname, $block->instance);
        $contentimages = $configdata->contentimages;
        unset($configdata->contentimages);
        $imagefilenames = array_keys($configdata->images);
        $imagedestfilenames = array_values($configdata->images);
        unset($configdata->images);
        $this->upload_files_in_block(
            $configdata,
            $contentimages,
            $imagefilenames,
            $imagedestfilenames
        );
        $block->instance_config_save($configdata);
        $block = block_instance_by_id($block->instance->id);
        return $block;
    }

    /**
     * Create frontpage instance
     *
     * @return moodle_page
     */
    protected function create_front_page() {
        $page = new moodle_page();
        // Watch: if the context is system, this is not the frontpage, this is another system page !!
        $page->set_context(context_course::instance(SITEID)); // Add it to the frontpage.
        $page->set_pagetype('site-index');
        $page->set_pagelayout('frontpage');
        return $page;
    }

    /**
     * Upload a file/image in the block
     *
     * @param object $configdata
     * @param array $contentimage
     * @param array $imagefilenames
     * @param array $destfilenames
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    protected function upload_files_in_block(&$configdata, $contentimage, $imagefilenames, $destfilenames) {
        $usercontext = context_user::instance($this->user->id);

        $draftcontentitemid = file_get_unused_draft_itemid();
        $imagehtml = '';
        $files = [];
        foreach ($contentimage as $index => $filename) {
            $files[] = $filename;
            $imagehtml .= '<img src="@@PLUGINFILE@@/' . basename($filename) . '" />';
        }
        $this->upload_image_draft_area($usercontext, $draftcontentitemid, $files, array());
        $text = '<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit,'
            . 'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim '
            . 'veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. '
            . 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. '
            . 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum </p>';
        if (!empty($configdata->text)) {
            $text = $configdata->text;
            $configdata->text = [];
        }
        $text .= $imagehtml;
        $configdata->text['itemid'] = $draftcontentitemid;
        $configdata->text['format'] = FORMAT_HTML;
        $configdata->text['text'] = $text;
        $draftitemid = file_get_unused_draft_itemid();
        $configdata->images = $draftitemid;
        $this->upload_image_draft_area($usercontext,
            $draftitemid,
            $imagefilenames,
            $destfilenames);
    }

    /**
     * Upload a file in draft area
     *
     * @param context_user $usercontext
     * @param int $draftitemid
     * @param array $filenames
     * @param array $destfilenames
     * @return array files identifiers
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    protected function upload_image_draft_area($usercontext, $draftitemid, $filenames, $destfilenames) {
        global $CFG;
        $filesid = [];
        foreach ($filenames as $index => $filename) {
            $destfilename = $filename;
            if (!empty($destfilenames[$index])) {
                $destfilename = $destfilenames[$index];
            }
            $filerecord = array(
                'contextid' => $usercontext->id,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => $draftitemid,
                'filepath' => '/',
                'filename' => $destfilename,
            );
            // Create an area to upload the file.
            $fs = get_file_storage();
            // Create a file from the string that we made earlier.
            if (!($file = $fs->get_file($filerecord['contextid'],
                $filerecord['component'],
                $filerecord['filearea'],
                $filerecord['itemid'],
                $filerecord['filepath'],
                $filerecord['filename']))) {
                $file = $fs->create_file_from_pathname($filerecord,
                    $CFG->dirroot . '/blocks/mcms/tests/fixtures/' . $filename);
            }
            $filesid[] = $file->get_itemid();
        }
        return $filesid;
    }

    /**
     * Filter content out so we can compare easily
     *
     * @param string $content
     * @param object $block
     * @return string|string[]
     */
    protected function filter_out_content($content, $block) {
        $content = preg_replace('/id="block-mcms([^"]+)"/i', '', $content);
        $content = str_replace("/{$block->context->id}/", '/BLOCKCONTEXTID/', $content);
        return $content;
    }

    /**
     * Get block hash
     *
     * @param block_base $block
     * @return array|string[]
     * @throws coding_exception
     */
    protected function get_block_file_hash($block) {
        $fs = get_file_storage();
        $block = block_instance_by_id($block->instance->id);
        $allfiles = $fs->get_area_files($block->context->id, 'block_mcms', 'images');
        return array_map(function($val) {
            return $val->get_contenthash();
        }, $allfiles);
    }
}

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
 * Admin tool presets plugin to load some settings.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tool_admin_presets\local\action\import;

/**
 * @param int $oldversion
 * @return bool
 */
function xmldb_tool_admin_presets_upgrade($oldversion): bool {
    global $DB, $CFG;

    // This part of code should be removed once the integration of admin_presets has been validated.
    // It is only intended to update the versions of moodle that have already integrated the branch concerned
    // by the addition of admin_preset to the core of moodle.
    if ($oldversion < 2021052701.08) {

        // Get files number to loop on and generate ur preset config.
        $filespath = $CFG->dirroot . '/admin/tool/admin_presets/db/presetsfiles';

        // FilesystemIterator::SKIP_DOTS this prevent some annoying things like .DS_STORE files ... to be count.
        $files = new FilesystemIterator($filespath, FilesystemIterator::SKIP_DOTS);
        $filesnumber = iterator_count($files);

        // Check if we have some files.
        if ($filesnumber != 0) {
            // Loop.
            foreach ($files as $file) {

                // Get file content.
                // Check if file is not XML type we skip.
                if (!(mime_content_type($file->getPathname()) != "application/xml")) {
                    continue;
                }

                $content = file_get_contents($file->getPathname());
                // Xml to obj.
                $xml = simplexml_load_string($content);

                $import = new import();
                $import->import_filecontent($xml, false);
            }
        }
    }
    return true;
}

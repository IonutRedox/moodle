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
 * Workflow course format.  Display the whole course as "steps" made of modules.
 *
 * @package format_workflow
 * @copyright 2021 Ionut Mugurel Cinjau
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

// Horrible backwards compatible parameter aliasing..
if ($step = optional_param('topic', 0, PARAM_INT)) {
    $url = $PAGE->url;
    $url->param('section', $step);
    debugging('Outdated step param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing..

$context = context_course::instance($course->id);
// Retrieve course format option fields and add them to the $course object.
$course = course_get_format($course)->get_course();
$course->coursedisplay = COURSE_DISPLAY_SINGLEPAGE;
$course->hiddensections = 0;

if (($marker >=0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure all needed sections in format_workflow are created
$sectionnum = 5;
for($index = 0; $index < $sectionnum; $index++){
    course_create_sections_if_missing($course, $index);
}

$renderer = $PAGE->get_renderer('format_workflow');

if (empty($displaysection)) {
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module
$PAGE->requires->js('/course/format/workflow/format.js');

<?php

//--------------FUNCION PARA LA REDIRECCION AL CENTRO DE CALIFICACIONES DESDE ZAJUNA-------------------
/* public function add_course_essentials($coursenode, stdClass $course) {
    // ANEXAR COMO VARIABLE GLOBAL $USER PARA IDENTIFICAR EL USUARIO LOGEADO
    global $CFG, $SITE, $USER;
    require_once($CFG->dirroot . '/course/lib.php');

    if ($course->id == $SITE->id) {
        return $this->add_front_page_course_essentials($coursenode, $course);
    }

    if ($coursenode == false || !($coursenode instanceof navigation_node) || $coursenode->get('participants', navigation_node::TYPE_CONTAINER)) {
        return true;
    }

    $navoptions = course_get_user_navigation_options($this->page->context, $course);

    //Participants
    if ($navoptions->participants) {
        $participants = $coursenode->add(get_string('participants'), new moodle_url('/user/index.php?id='.$course->id),
            self::TYPE_CONTAINER, get_string('participants'), 'participants', new pix_icon('i/users', ''));

        if ($navoptions->blogs) {
            $blogsurls = new moodle_url('/blog/index.php');
            if ($currentgroup = groups_get_course_group($course, true)) {
                $blogsurls->param('groupid', $currentgroup);
            } else {
                $blogsurls->param('courseid', $course->id);
            }
            $participants->add(get_string('blogscourse', 'blog'), $blogsurls->out(), self::TYPE_SETTING, null, 'courseblogs');
        }

        if ($navoptions->notes) {
            $participants->add(get_string('notes', 'notes'), new moodle_url('/notes/index.php', array('filtertype' => 'course', 'filterselect' => $course->id)), self::TYPE_SETTING, null, 'currentcoursenotes');
        }
    } else if (count($this->extendforuser) > 0) {
        $coursenode->add(get_string('participants'), null, self::TYPE_CONTAINER, get_string('participants'), 'participants');
    }

    // Badges.
    if ($navoptions->badges) {
        $url = new moodle_url('/badges/view.php', array('type' => 2, 'id' => $course->id));

        $coursenode->add(get_string('coursebadges', 'badges'), $url,
                navigation_node::TYPE_SETTING, null, 'badgesview',
                new pix_icon('i/badge', get_string('coursebadges', 'badges')));
    }

    // Check access to the course and competencies page.
    if ($navoptions->competencies) {
        // Just a link to course competency.
        $title = get_string('competencies', 'core_competency');
        $path = new moodle_url("/admin/tool/lp/coursecompetencies.php", array('courseid' => $course->id));
        $coursenode->add($title, $path, navigation_node::TYPE_SETTING, null, 'competencies',
                new pix_icon('i/competencies', ''));
    } */

//-----------------------------FUNCION REDIRECCIONAMIENTO AL NUEVO CENTRO DE CALIFICACIONES LMS-----------------------------------------
if ($navoptions->grades) {
    $external_url = 'http://localhost/lms-califica/config/login_config.php?user=' . $USER->id . '&idnumber=' . urlencode($course->idnumber);
    $url = new moodle_url($external_url);
    $gradenode = $coursenode->add(
        get_string('grades'),
        $url,
        self::TYPE_SETTING,
        null,
        'grades',
        new pix_icon('i/grades', '')
    );
    if ($this->page->context->contextlevel < CONTEXT_MODULE && strpos($this->page->pagetype, 'grade-') === 0) {
        $gradenode->make_active();
    }
}

$new_url = new moodle_url('http://localhost/lmsActividades/config/login_config.php', array(
    'user' => base64_encode($USER->id),
    'idnumber' => base64_encode($course->id),
    'redirect' => 'actividades' // Indica que se redirigirá a actividades.php después de login_config.php
));
$new_node = $coursenode->add(
    'Centro de Calificaciones',
    $new_url,
    self::TYPE_SETTING,
    null,
    'newpage',
    new pix_icon('i/grades', 'Centro de Calificaciones')
);
//-------------------NO MODICIAR NADA MAS EN ESTA FUNCION--------------------------------------

/*   // Add link for configuring communication.
    if ($navoptions->communication) {
        $url = new moodle_url('/communication/configure.php', [
            'contextid' => \core\context\course::instance($course->id)->id,
            'instanceid' => $course->id,
            'instancetype' => 'coursecommunication',
            'component' => 'core_course',
        ]);
        $coursenode->add(get_string('communication', 'communication'), $url,
            navigation_node::TYPE_SETTING, null, 'communication');
    }

    return true;
} */
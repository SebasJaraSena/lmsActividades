--------------FUNCION PARA LA REDIRECCION AL CENTRO DE CALIFICACIONES DESDE ZAJUNA-------------------
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

---------------FUNCION REDIRECCIONAMIENTO AL NUEVO CENTRO DE CALIFICACIONES LMS--------------------- 
if ($navoptions->grades) {
    $external_url = 'http://localhost/lms/config/login_config.php?user=' . $USER->id . '&idnumber=' . urlencode($course->idnumber);
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
-------------------NO MODICIAR NADA MAS EN ESTA FUNCION--------------------------------------

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

--
----------------------FUNCION PARA OBTENER LA SESSION DEL USUARIO LOGUEADO----------------

CREATE OR REPLACE FUNCTION obtenerSession(curso BIGINT, iduser BIGINT)
RETURNS VOID AS
$$
begin
	RAISE NOTICE 'Inicio de la función';
    -- Crear la tabla temporal
    CREATE TEMPORARY TABLE IF NOT EXISTS tablaTempSes(
        userid BIGINT,
        username VARCHAR,
        firstname VARCHAR,
        lastname VARCHAR,
        email VARCHAR,
        shortname VARCHAR,
        tipo_user BIGINT
    );

    -- Insertar los datos en la tabla temporal
    INSERT INTO tablaTempSes(userid, username, firstname, lastname, email, shortname, tipo_user)
    
    SELECT  
    u.id as userid, 
    u.username, 
    u.firstname, 
    u.lastname, 
    u.email, 
    r.shortname, 
    ra.roleid as tipo_user
    
    FROM mdl_user u
    JOIN mdl_user_enrolments ue ON ue.userid = u.id
    JOIN mdl_enrol e ON e.id = ue.enrolid
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_course mc ON mc.id = e.courseid
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE u.id = iduser AND mc.id = curso;

    -- Crear la vista a partir de la tabla temporal
    CREATE OR REPLACE VIEW vista_ses AS 
    SELECT * FROM tablaTempSes;
   
END;
$$
LANGUAGE 'plpgsql';

--
------------FUNCION PARA OBTENER LAS CATEGORIAS DE EVALUACION DE ZAJUNA

CREATE OR REPLACE FUNCTION obtenerCategorias(curso BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempCat(
id BIGINT,
fullname VARCHAR
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempCat(id, fullname)

SELECT id, fullname 
FROM public.mdl_grade_categories
WHERE courseid = curso;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_cat AS
SELECT * FROM tablaTempCat;
END;
$$
LANGUAGE 'plpgsql';

--
--------------FUNCION PARA OBTENER LAS ACTIVIDADES REALIZADAS EN ZAJUNA----------------------------

CREATE OR REPLACE FUNCTION obtenerActividades(course BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTemp(
id BIGINT,
itemname VARCHAR,
idacti BIGINT,
idnumber VARCHAR,
courseid BIGINT,
categoryid BIGINT,
fullname VARCHAR,
itemmodule VARCHAR
);
-- Insertar los datos en la tabla temporal
INSERT INTO tablaTemp(id, itemname, idacti, idnumber, courseid, categoryid, fullname, itemmodule)
SELECT i.id, i.itemname, i.iteminstance, c.idnumber, c.id, i.categoryid, g.fullname, i.itemmodule
FROM mdl_course c
JOIN mdl_grade_items i ON c.id = i.courseid
JOIN mdl_grade_categories g ON i.categoryid = g.id
WHERE c.id = course AND i.itemmodule = 'quiz';

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_actividades AS
SELECT * FROM tablaTemp;
END;
$$
LANGUAGE 'plpgsql';

--
---------------FUNCION PARA OBTENER LAS ACTIVIDADES FILTRADAS POR CATEGORIA--------------------------

CREATE OR REPLACE FUNCTION obtenerActividadesAp(course BIGINT, idrea VARCHAR)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempAp(
id BIGINT,
itemname VARCHAR,
idacti BIGINT,
idnumber VARCHAR,
courseid BIGINT,
categoryid BIGINT,
fullname VARCHAR,
itemmodule VARCHAR
);
-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempAp(id, itemname, idacti, idnumber, courseid, categoryid, fullname, itemmodule)
SELECT i.id, i.itemname, i.iteminstance, c.idnumber, c.id, i.categoryid, g.fullname, i.itemmodule
FROM mdl_course c
JOIN mdl_grade_items i ON c.id = i.courseid
JOIN mdl_grade_categories g ON i.categoryid = g.id
WHERE c.id = course AND i.itemmodule = 'quiz' and g.fullname = idrea;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_actividadesAp AS
SELECT * FROM tablaTempAp;
END;
$$
LANGUAGE 'plpgsql';

--
---------------FUNCION PARA OBTENER LAS EVIDENCIAS REALIZADAS EN UN CURSO------------------------------

CREATE OR REPLACE FUNCTION obtenerEvidencias(course BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempEvi(
id BIGINT,
itemname VARCHAR,
idacti BIGINT,
idnumber VARCHAR,
courseid BIGINT,
categoryid BIGINT,
fullname VARCHAR,
itemmodule VARCHAR
);
-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempEvi(id, itemname, idacti, idnumber, courseid, categoryid, fullname, itemmodule)
SELECT i.id, i.itemname, i.iteminstance, c.idnumber, c.id, i.categoryid, g.fullname, i.itemmodule
FROM mdl_course c
JOIN mdl_grade_items i ON c.id = i.courseid
JOIN mdl_grade_categories g ON i.categoryid = g.id
WHERE c.id = course AND i.itemmodule = 'assign';

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_evidencias AS
SELECT * FROM tablaTempEvi;
END;
$$
LANGUAGE 'plpgsql';

--
-------------------FUNCION PARA OBTENER LAS EVIDENCIAS FILTRADAS POR CATEGORIA----------------------------

CREATE OR REPLACE FUNCTION obtenerEvidenciasAp(course BIGINT, idrea VARCHAR)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempEviAp(
id BIGINT,
itemname VARCHAR,
idacti BIGINT,
idnumber VARCHAR,
courseid BIGINT,
categoryid BIGINT,
fullname VARCHAR,
itemmodule VARCHAR
);
-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempEviAp(id, itemname, idacti, idnumber, courseid, categoryid, fullname, itemmodule)
SELECT i.id, i.itemname, i.iteminstance, c.idnumber, c.id, i.categoryid, g.fullname, i.itemmodule
FROM mdl_course c
JOIN mdl_grade_items i ON c.id = i.courseid
JOIN mdl_grade_categories g ON i.categoryid = g.id
WHERE c.id = course AND i.itemmodule = 'assign' and g.fullname = idrea;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_evidenciasAp AS
SELECT * FROM tablaTempEviAp;
END;
$$
LANGUAGE 'plpgsql';

--
-------------------FUNCION PARA OBTENER LOS FOROS REALIZADOS EN UN CURSO-----------------------------

CREATE OR REPLACE FUNCTION obtenerForos(course BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempFor(
id BIGINT,
itemname VARCHAR,
idacti BIGINT,
idnumber VARCHAR,
courseid BIGINT,
categoryid BIGINT,
fullname VARCHAR,
itemmodule VARCHAR
);
-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempFor(id, itemname, idacti, idnumber, courseid, categoryid, fullname, itemmodule)

SELECT i.id,  TRIM(REPLACE(REPLACE(i.itemname, 'rating', ''), 'calificación', '')) AS itemname, i.iteminstance, c.idnumber, c.id, i.categoryid, g.fullname, i.itemmodule

FROM mdl_course c
JOIN mdl_grade_items i ON c.id = i.courseid
JOIN mdl_grade_categories g ON i.categoryid = g.id
WHERE c.id = course AND (i.itemname LIKE '%rating%' OR i.itemname LIKE '%calificación%');

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_foros AS
SELECT * FROM tablaTempFor;
END;
$$
LANGUAGE 'plpgsql';

--
-------------------FUNCION PARA OBTENER LOS FOROS FILTRADOS POR CATEGORIA----------------

CREATE OR REPLACE FUNCTION obtenerForosAp(course BIGINT, idrea VARCHAR)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempForAp(
id BIGINT,
itemname VARCHAR,
idacti BIGINT,
idnumber VARCHAR,
courseid BIGINT,
categoryid BIGINT,
fullname VARCHAR,
itemmodule VARCHAR
);
-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempForAp(id, itemname, idacti, idnumber, courseid, categoryid, fullname, itemmodule)

SELECT i.id, TRIM(REPLACE(REPLACE(i.itemname, 'rating', ''), 'calificación', '')) AS itemname, i.iteminstance, c.idnumber, c.id, i.categoryid, g.fullname, i.itemmodule

FROM mdl_course c
JOIN mdl_grade_items i ON c.id = i.courseid
JOIN mdl_grade_categories g ON i.categoryid = g.id
WHERE c.id = course AND i.itemmodule = 'forum' and g.fullname = idrea AND (i.itemname LIKE '%rating%' OR i.itemname LIKE '%calificación%');

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_forosAp AS
SELECT * FROM tablaTempForAp;
END;
$$
LANGUAGE 'plpgsql';

--
----------------------FUNCION PARA OBTENER LAS WKIS DE UN CURSO---------------------------------

CREATE OR REPLACE FUNCTION obtenerWikis(curso BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempWik(
id BIGINT,
course BIGINT,
nombre VARCHAR

);
-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempWik(id, course, nombre)

SELECT w.id, w.course, w.name as nombre

FROM mdl_wiki w
WHERE w.course = curso;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_wik AS
SELECT * FROM tablaTempWik;

END;
$$
LANGUAGE 'plpgsql';

--
-----------------------FUNCION PARA OBTENER LA PARTICIPACION DE LAS WIKIS DE UN CURSO--------------------------------

CREATE OR REPLACE FUNCTION obtenerParticipacionWiki(curso BIGINT, id_user BIGINT[], acti BIGINT[])
RETURNS TABLE(
    userid BIGINT,
    content text,
    id BIGINT,
    name VARCHAR,
    course BIGINT
)
AS $$
DECLARE
    i INT;
BEGIN

    -- Itera sobre los arrays y ejecuta la consulta para cada par de elementos
    FOR i IN 1..array_length(id_user, 1) LOOP
        RETURN QUERY
        
        SELECT wv.userid, wv.content, w.id, w.name, w.course
        
		FROM mdl_wiki_versions wv
		JOIN mdl_wiki_subwikis ws ON ws.id = wv.pageid
		JOIN mdl_wiki w ON w.id = ws.wikiid
		
        WHERE wv.userid = id_user[i] and w.id = acti[i] and w.course = curso;
       
    END LOOP;
END;
$$
LANGUAGE 'plpgsql';

--
-----------------FUNCION PARA OBTENER LOS USUARIOS MATRICULADOS EN UN CURSO---------------------

CREATE OR REPLACE FUNCTION obtenerUsuarios(course BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempUsr(
id BIGINT,
username VARCHAR,
firstname VARCHAR,
lastname VARCHAR,
email VARCHAR,
courseid BIGINT,
shortname VARCHAR,
idnumber VARCHAR
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempUsr(id, username, firstname, lastname, email, courseid, shortname, idnumber)
SELECT u.id, u.username, u.firstname, u.lastname, u.email, e.courseid, r.shortname, mc.idnumber
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_role r ON r.id = ra.roleid
WHERE mc.id = course and r.shortname = 'student';

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_usuarios AS
SELECT * FROM tablaTempUsr;
END;
$$
LANGUAGE 'plpgsql';

--
------------------FUNCION PARA OBTENER SOLO EL APRENDIZ LOGUEADO DE UN CURSO--------------------------

CREATE OR REPLACE FUNCTION obtenerUsuariosApren(course BIGINT, iduser BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempUsrApr(
id BIGINT,
username VARCHAR,
firstname VARCHAR,
lastname VARCHAR,
email VARCHAR,
courseid BIGINT,
shortname VARCHAR,
idnumber VARCHAR
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempUsrApr(id, username, firstname, lastname, email, courseid, shortname, idnumber)
SELECT u.id, u.username, u.firstname, u.lastname, u.email, e.courseid, r.shortname, mc.idnumber
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_role r ON r.id = ra.roleid
WHERE mc.id = course and r.shortname = 'student' and u.id = iduser;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_usuarios_apren AS
SELECT * FROM tablaTempUsrApr;
END;
$$
LANGUAGE 'plpgsql';

--
--------------------FUNCION PARA OBTENER LAS NOTAS DE LAS PRUEBAS DE CONOCIMIENTO-----------------------

CREATE OR REPLACE FUNCTION obtenerNotasActi(idacti BIGINT[], id_user BIGINT[])
RETURNS TABLE (
quiz BIGINT,
userid BIGINT,
grade NUMERIC
) AS
$$
DECLARE
i INT;
BEGIN
IF array_length(idacti, 1) != array_length(id_user, 1) THEN
RAISE EXCEPTION 'Los arrays deben tener la misma longitud';
END IF;

FOR i IN 1..array_length(idacti, 1) LOOP
RETURN QUERY
SELECT q.quiz, q.userid, q.grade
FROM mdl_quiz_grades q
WHERE q.quiz = idacti[i] AND q.userid = id_user[i];
END LOOP;
END;
$$
LANGUAGE 'plpgsql';

--
----------------------FUNCION PARA OBTENER LAS NOTAS DE LOS FOROS----------------------------

CREATE OR REPLACE FUNCTION obtenerNotasFor(id_curso BIGINT, id_user BIGINT[], id_for BIGINT[])
RETURNS TABLE (
userid BIGINT,
rawgrade NUMERIC,
iteminstance BIGINT,
idnumber VARCHAR,
fullname VARCHAR,
feedback TEXT
) AS
$$
DECLARE
i INT;
BEGIN
IF array_length(id_user, 1) != array_length(id_for, 1) THEN
RAISE EXCEPTION 'Los arrays deben tener la misma longitud';
END IF;

FOR i IN 1..array_length(id_user, 1) LOOP
RETURN QUERY

SELECT DISTINCT g.userid, g.rawgrade, f.id, c.idnumber, c.fullname, g.feedback

FROM mdl_grade_items gi
JOIN mdl_grade_grades g ON g.itemid = gi.id
JOIN mdl_forum f ON f.id = gi.iteminstance
JOIN mdl_course c ON f.course = c.id
where c.id = id_curso AND f.id = id_for[i] AND g.userid = id_user[i] AND (gi.itemname LIKE '%rating%' OR gi.itemname LIKE '%calificación%') ;
END LOOP;
END;
$$
LANGUAGE 'plpgsql';

--
----------------------FUNCION PARA OBTENER LA PARTICIPACION DE UN APRENDIZ EN UN FORO

CREATE OR REPLACE FUNCTION obtenerParticipacionFor(id_for BIGINT[], id_user BIGINT[])
RETURNS TABLE(
    id BIGINT,
    userid BIGINT,
    mensaje TEXT
)
AS $$
DECLARE
    i INT;
BEGIN
    -- Verifica que ambos arrays tengan la misma longitud
    IF array_length(id_user, 1) != array_length(id_for, 1) THEN
        RAISE EXCEPTION 'Los arrays deben tener la misma longitud';
    END IF;

    -- Itera sobre los arrays y ejecuta la consulta para cada par de elementos
    FOR i IN 1..array_length(id_user, 1) LOOP
        RETURN QUERY
        SELECT fp.id, fp.userid, fp.message

        FROM mdl_forum_posts fp
        JOIN mdl_forum_discussions fd ON fp.discussion = fd.id
        JOIN mdl_forum f ON fd.forum = f.id
        WHERE f.id = id_for[i] AND fp.userid = id_user[i];

    END LOOP;
END;
$$
LANGUAGE 'plpgsql';

--
--------------------FUNCION PARA OBTENER LAS NOTAS DE LAS EVIDENCIAS--------------------

CREATE OR REPLACE FUNCTION obtenerNotasEvi(id_evi BIGINT[], id_user BIGINT[])
RETURNS TABLE (
rawgrade NUMERIC,
feedback TEXT,
itemmodule VARCHAR
) AS
$$
DECLARE
i INT;
BEGIN
IF array_length(id_evi, 1) != array_length(id_user, 1) THEN
RAISE EXCEPTION 'Los arrays deben tener la misma longitud';
END IF;

FOR i IN 1..array_length(id_evi, 1) LOOP
RETURN QUERY
SELECT g.rawgrade, g.feedback, gi.itemmodule
FROM mdl_grade_items gi
JOIN mdl_grade_grades g ON gi.id = g.itemid
WHERE g.userid = id_user[i] AND gi.iteminstance = id_evi[i] AND gi.itemmodule = 'assign';
END LOOP;
END;
$$
LANGUAGE 'plpgsql';

--
--------------------FUNCION PARA OBTENER LAS PARTICIPACIONES DE LAS EVIDENCIAS

CREATE OR REPLACE FUNCTION obtenerParticipacionEvi(id_evi BIGINT[], id_user BIGINT[])
RETURNS TABLE(
    id BIGINT,
    userid BIGINT,
    status VARCHAR
)
AS $$
DECLARE
    i INT;
BEGIN
    -- Verifica que ambos arrays tengan la misma longitud
    IF array_length(id_user, 1) != array_length(id_evi, 1) THEN
        RAISE EXCEPTION 'Los arrays deben tener la misma longitud';
    END IF;

    -- Itera sobre los arrays y ejecuta la consulta para cada par de elementos
    FOR i IN 1..array_length(id_user, 1) LOOP
        RETURN QUERY
        
        SELECT assignment, userid, status
		FROM mdl_assign_submission
		WHERE assignment = id_evi[i] and userid = id_user[i] and status = 'submitted'; 
       
    END LOOP;
END;
$$
LANGUAGE 'plpgsql';

--
----------------FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCION DE A REVISIONES DE ZAJUNA-------------------

CREATE OR REPLACE FUNCTION obtenerParametros(id_user BIGINT[], curso BIGINT, acti BIGINT[])
RETURNS TABLE (
gradeid BIGINT,
itemid BIGINT,
userid BIGINT,
id BIGINT,
instance BIGINT,
rawgrade NUMERIC,
module BIGINT,
idnumber VARCHAR,
idattemp BIGINT
) AS
$$
BEGIN
RETURN QUERY
WITH temp AS (
SELECT unnest(id_user) as userid, unnest(acti) as acti, generate_series(1, array_length(id_user, 1)) as idx
)
SELECT
g.id as gradeid, g.itemid, g.userid, c.id, c.instance, g.rawgrade, c.module, mc.idnumber, a.id as idattemp
FROM temp
JOIN mdl_grade_grades g ON g.userid = temp.userid
JOIN mdl_user u ON u.id = g.userid
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_course_modules c ON c.course = mc.id
JOIN mdl_quiz_attempts a on c.instance = a.quiz
WHERE a.userid = temp.userid AND mc.id = curso AND c.instance = temp.acti AND c.module = 17;
END;
$$
LANGUAGE plpgsql;

--
-----------------FUNCION PARA OBTENER PARAMETROS DE REDIRECCION PARA ACTIVIDADES PENDIENTES---------------------

CREATE OR REPLACE FUNCTION obtenerParametrosPend(acti BIGINT[])
RETURNS TABLE (
id BIGINT
) AS
$$
BEGIN
RETURN QUERY
WITH temp AS (
SELECT unnest(acti) as acti, generate_series(1, array_length(acti, 1)) as idx
)
SELECT
c.id
FROM temp
JOIN mdl_course_modules c ON c.instance
WHERE c.module = 17 AND c.instance = temp.acti;
END;
$$
LANGUAGE plpgsql;

--
------------------FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCION EN FOROS-------------------------

CREATE OR REPLACE FUNCTION obtenerParametrosFor(id_for BIGINT[])
RETURNS TABLE (
id BIGINT
) AS
$$
BEGIN
RETURN QUERY
WITH temp AS (
SELECT unnest(id_for) as id_for, generate_series(1, array_length(id_for, 1)) as idx
)
SELECT
d.id
FROM temp
JOIN mdl_forum_discussions d ON d.forum = temp.id_for;
END;
$$
LANGUAGE plpgsql;

--
------------------------FUNCION PARA OBTENER PARAMETROS DE REDIRECCION DE REVISION DE FOROS PENDIENTES---------------------

CREATE OR REPLACE FUNCTION public.obtenerparametrospendfor(id_for bigint[])
RETURNS TABLE(id bigint)
AS
$$
BEGIN
RETURN QUERY
WITH temp AS (
SELECT unnest(id_for) as id_for
)
SELECT
c.id
FROM temp
JOIN mdl_course_modules c ON c.instance = temp.id_for
WHERE c.module = 9;
END;
$$

LANGUAGE plpgsql;

--
------------------------FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCION DE REVISION DE EVIDENCIAS--------------------------

CREATE OR REPLACE FUNCTION obtenerParametrosEvi(id_curso BIGINT, id_evi BIGINT[])
RETURNS TABLE (
id BIGINT,
instance BIGINT,
module BIGINT,
idnumber VARCHAR
) AS
$$
BEGIN
RETURN QUERY
WITH temp AS (
SELECT unnest(id_evi) as id_evi, generate_series(1, array_length(id_evi, 1)) as idx
)
SELECT
cm.id,
cm.instance,
cm.module,
c.idnumber
FROM temp
JOIN mdl_course_modules cm ON cm.instance = temp.id_evi
JOIN mdl_course c ON c.id = cm.course
WHERE cm.instance = temp.id_evi AND c.id = id_curso AND cm.module = 1;
END;
$$
LANGUAGE plpgsql;

--
-----------------FUNCION PARA OBTENER EL ID DE REDIRECCION A LA REVISION DE WIKIS

CREATE OR REPLACE FUNCTION obtenerParametrosWiki(curso BIGINT, acti BIGINT[])
RETURNS TABLE (
    id BIGINT
) AS
$$
BEGIN
    RETURN QUERY
    WITH temp AS (
        SELECT unnest(acti) as acti, generate_series(1, array_length(acti, 1)) as idx
    )
    SELECT
        c.id
    FROM temp
    JOIN mdl_course_modules c ON c.instance = temp.acti
    WHERE c.course = curso;
END;
$$
LANGUAGE plpgsql;

--
--------------------FUNCION PARA OBTENER EL ID DE REDIRECCION A LA ESCALA DE CALIFICACIONES----------------

CREATE OR REPLACE FUNCTION obtenerEscala(curso BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempEsca(
id BIGINT
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempEsca(id)
SELECT t.id
FROM mdl_context t
JOIN mdl_course c ON c.id = t.instanceid
WHERE c.id = curso;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_esca AS
SELECT * FROM tablaTempEsca;
END;
$$
LANGUAGE 'plpgsql';
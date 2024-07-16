-------------------VISTA EN LA BASE DE DATOS ZAJUNA--------------------

-- Vista para consultas a actividades, foros y evidencias de zajuna con sus notas y sus categorias
CREATE VIEW "public".vista_acti as SELECT distinct i.id, i.itemname, i.iteminstance as idacti, 
c.idnumber, c.id as courseid, i.categoryid, g.fullname, i.itemmodule
FROM mdl_course c
JOIN mdl_grade_items i ON c.id = i.courseid
JOIN mdl_grade_categories g ON i.categoryid = g.id

-- Vista para consultas a usuarios de los cursos de zajuna
CREATE VIEW "public".vista_users as SELECT distinct u.id, u.username, u.firstname, u.lastname, u.email, e.courseid, r.shortname, mc.idnumber
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_role r ON r.id = ra.roleid

-- vista para obtener parametros para enviar por url a actividades zajuna
create view "public".vista_param as 
SELECT DISTINCT g.id as gradeid, g.itemid, g.userid, c.id, c.instance, g.rawgrade, c.module, mc.idnumber
FROM mdl_grade_grades g
JOIN mdl_user u ON u.id = g.userid
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_course_modules c ON c.course = mc.id

--vista para obtener las notas de los foros de zajuna de los aprendices 
create view "public".vista_notasfor as SELECT DISTINCT g.userid, g.rawgrade, i.iteminstance, c.idnumber, c.fullname, g.feedback
FROM mdl_grade_items i
JOIN mdl_grade_grades g ON g.itemid = i.id
JOIN mdl_forum f ON f.id = i.iteminstance
JOIN mdl_course c ON f.course = c.id  

--VISTA PARA OBTENER LOS PARAMETROS DE REDIRECCIONAMIENTO A REVISION DE EVIDENCIAS ZAJUNA
create view "public".vista_paramevi as SELECT cm.id, cm.instance, cm.module, c.idnumber
FROM mdl_course_modules cm
JOIN mdl_course c ON c.id = cm.course


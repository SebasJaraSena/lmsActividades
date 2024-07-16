--
-------------------CREACION DE SERVIDOR REMOTO PARA EXPORTAR TABLAS DE INTEGRACION--------------------

-- En ambas bases de datos
CREATE EXTENSION IF NOT EXISTS postgres_fdw;

-- Crear el servidor remoto para la DB integracion_replica, se instala en la base de datos de ZAJUNA
CREATE SERVER integracion_server
FOREIGN DATA WRAPPER postgres_fdw
OPTIONS (host 'localhost', dbname 'integracion_replica-3', port '5432');

-- Crear el mapeo de usuario
CREATE USER MAPPING FOR CURRENT_USER
SERVER integracion_server
OPTIONS (user 'postgres', password '12345');

--
IMPORT FOREIGN SCHEMA "INTEGRACION"
LIMIT TO ("USUARIO_LMS", "TABLA_NOTASXRA_CC", "RESULTADO_APRENDIZAJE")
FROM SERVER integracion_server
INTO public;

--
------------------FUNCION PARA OBTENER RESULTADOS DE APRENDIZAJE CON USUARIOS DE ZAJUNA-----------------------

CREATE OR REPLACE FUNCTION obtenerResultados(curso VARCHAR, competencia VARCHAR)
RETURNS VOID AS
$$
begin
RAISE NOTICE 'Inicio de la función';
-- Crear la tabla temporal
CREATE TEMPORARY TABLE IF NOT EXISTS tablaTemp(
"ID_USUARIO_LMS" BIGINT,
"USR_NUM_DOC" VARCHAR,
"USR_NOMBRE" VARCHAR,
"USR_APELLIDO" VARCHAR,
"FIC_ID" VARCHAR,
"CMP_ID" BIGINT,
"REA_ID" BIGINT,
"REA_NOMBRE" VARCHAR,
"ADR_ID" BIGINT,
"ADR_EVALUACION_RESULTADO" VARCHAR,
"ESTADO_SINCRONIZACION" BIGINT
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTemp("ID_USUARIO_LMS", "USR_NUM_DOC", "USR_NOMBRE", "USR_APELLIDO", "FIC_ID", "CMP_ID", "REA_ID", "REA_NOMBRE", "ADR_ID", "ADR_EVALUACION_RESULTADO", "ESTADO_SINCRONIZACION")
SELECT
u.id AS "ID_USUARIO_LMS",
u.username AS "USR_NUM_DOC",
u.firstname AS "USR_NOMBRE",
u.lastname AS "USR_APELLIDO",
mc.idnumber AS "FIC_ID",
adr."CMP_ID",
rap."REA_ID",
rap."REA_NOMBRE",
adr."ADR_ID",
adr."ADR_EVALUACION_RESULTADO",
adr."ESTADO_SINCRONIZACION"
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_role r ON r.id = ra.roleid
JOIN public."TABLA_NOTASXRA_CC" adr ON u.id = adr."LMS_ID"
JOIN public."RESULTADO_APRENDIZAJE" rap ON adr."REA_ID" = rap."REA_ID"
WHERE mc.idnumber = curso AND adr."CMP_ID" = competencia::BIGINT
ORDER BY u.firstname ASC;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_result AS
SELECT * FROM tablaTemp;

END;
$$
LANGUAGE 'plpgsql';

--
--------------------FUNCION PARA OBTENER LOS RESULTADOSAP CON CONSUMO TOTAL DE INTEGRACION_REPLICA-------------------------
--------NO SE USA ACTUALMENTE----------

CREATE OR REPLACE FUNCTION "INTEGRACION".obtenerResultadosAp(curso VARCHAR, competencia VARCHAR)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE IF NOT EXISTS tablaTemp(
"ID_USUARIO_LMS" BIGINT,
"USR_NUM_DOC" VARCHAR,
"USR_NOMBRE" VARCHAR,
"USR_APELLIDO" VARCHAR,
"FIC_ID" BIGINT,
"CMP_ID" BIGINT,
"REA_ID" BIGINT,
"REA_NOMBRE" VARCHAR,
"ADR_ID" BIGINT,
"ADR_EVALUACION_RESULTADO" VARCHAR,
"ESTADO_SINCRONIZACION" BIGINT
) ON COMMIT DROP; -- Se eliminará al final de la transacción

-- Limpiar la tabla temporal si ya existe
TRUNCATE TABLE tablaTemp;

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTemp("ID_USUARIO_LMS", "USR_NUM_DOC", "USR_NOMBRE", "USR_APELLIDO", "FIC_ID", "CMP_ID", "REA_ID", "REA_NOMBRE", "ADR_ID", "ADR_EVALUACION_RESULTADO", "ESTADO_SINCRONIZACION")
SELECT
"U"."ID_USUARIO_LMS",
"U"."USR_NUM_DOC",
"U"."USR_NOMBRE",
"U"."USR_APELLIDO",
"U"."FIC_ID",
"ADR"."CMP_ID",
"RA"."REA_ID",
"RA"."REA_NOMBRE",
"ADR"."ADR_ID",
"ADR"."ADR_EVALUACION_RESULTADO",
"ADR"."ESTADO_SINCRONIZACION"
FROM "INTEGRACION"."USUARIO_LMS" "U"
JOIN "INTEGRACION"."TABLA_NOTASXRA_CC" "ADR" ON "U"."LMS_ID" = "ADR"."LMS_ID"
JOIN "INTEGRACION"."RESULTADO_APRENDIZAJE" "RA" ON "ADR"."REA_ID" = "RA"."REA_ID"
WHERE "U"."FIC_ID" = curso::BIGINT AND "ADR"."CMP_ID" = competencia::BIGINT
ORDER BY "USR_NOMBRE" ASC;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_resultap AS
SELECT * FROM tablaTemp;
END;
$$
LANGUAGE 'plpgsql';


--
---------------------FUNCION PARA OBTENER LOS RESULTADOSAP POR APRENDIZ CON USUARIO DE ZAJUNA-------------------

CREATE OR REPLACE FUNCTION obtenerResultadosAprendiz(curso VARCHAR, competencia VARCHAR, user_id BIGINT)
RETURNS VOID AS
$$
begin
RAISE NOTICE 'Inicio de la función';
-- Crear la tabla temporal
CREATE TEMPORARY TABLE IF NOT EXISTS tablaTempApr(
"LMS_ID" BIGINT,
"USR_NUM_DOC" VARCHAR,
"USR_NOMBRE" VARCHAR,
"USR_APELLIDO" VARCHAR,
"FIC_ID" VARCHAR,
"CMP_ID" BIGINT,
"REA_ID" BIGINT,
"REA_NOMBRE" VARCHAR,
"ADR_ID" BIGINT,
"ADR_EVALUACION_RESULTADO" VARCHAR,
"ESTADO_SINCRONIZACION" BIGINT
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempApr("LMS_ID", "USR_NUM_DOC", "USR_NOMBRE", "USR_APELLIDO", "FIC_ID", "CMP_ID", "REA_ID", "REA_NOMBRE", "ADR_ID", "ADR_EVALUACION_RESULTADO", "ESTADO_SINCRONIZACION")
SELECT
u.id AS LMS_ID,
u.username AS "USR_NUM_DOC",
u.firstname AS "USR_NOMBRE",
u.lastname AS "USR_APELLIDO",
mc.idnumber AS "FIC_ID",
adr."CMP_ID",
rap."REA_ID",
rap."REA_NOMBRE",
adr."ADR_ID",
adr."ADR_EVALUACION_RESULTADO",
adr."ESTADO_SINCRONIZACION"
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_role r ON r.id = ra.roleid
JOIN public."TABLA_NOTASXRA_CC" adr ON u.id = adr."LMS_ID"
JOIN public."RESULTADO_APRENDIZAJE" rap ON adr."REA_ID" = rap."REA_ID"
WHERE mc.idnumber = curso AND adr."CMP_ID" = competencia::BIGINT AND u.id = user_id
ORDER BY u.firstname ASC;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_result AS
SELECT * FROM tablaTempApr;

END;
$$
LANGUAGE 'plpgsql';

--
-------------------FUNCION PARA OBTENER LOS RESULTADOSAP POR APRENDIZ CON CONSUMO TOTAL DE INTEGRACION_REPLICA-------------------------
------NO SE USA ACTUALMENTE----------

CREATE OR REPLACE FUNCTION "INTEGRACION".obtenerResultadosAprendiz(curso VARCHAR, competencia VARCHAR, userid BIGINT)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE IF NOT EXISTS tablaTemp(
"ID_USUARIO_LMS" BIGINT,
"USR_NUM_DOC" VARCHAR,
"USR_NOMBRE" VARCHAR,
"USR_APELLIDO" VARCHAR,
"FIC_ID" BIGINT,
"CMP_ID" BIGINT,
"REA_ID" BIGINT,
"REA_NOMBRE" VARCHAR,
"ADR_ID" BIGINT,
"ADR_EVALUACION_RESULTADO" VARCHAR,
"ESTADO_SINCRONIZACION" BIGINT
) ON COMMIT DROP; -- Se eliminará al final de la transacción

-- Limpiar la tabla temporal si ya existe
TRUNCATE TABLE tablaTemp;

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTemp("ID_USUARIO_LMS", "USR_NUM_DOC", "USR_NOMBRE", "USR_APELLIDO", "FIC_ID", "CMP_ID", "REA_ID", "REA_NOMBRE", "ADR_ID", "ADR_EVALUACION_RESULTADO", "ESTADO_SINCRONIZACION")
SELECT
"U"."ID_USUARIO_LMS",
"U"."USR_NUM_DOC",
"U"."USR_NOMBRE",
"U"."USR_APELLIDO",
"U"."FIC_ID",
"ADR"."CMP_ID",
"RA"."REA_ID",
"RA"."REA_NOMBRE",
"ADR"."ADR_ID",
"ADR"."ADR_EVALUACION_RESULTADO",
"ADR"."ESTADO_SINCRONIZACION"
FROM "INTEGRACION"."USUARIO_LMS" "U"
JOIN "INTEGRACION"."TABLA_NOTASXRA_CC" "ADR" ON "U"."LMS_ID" = "ADR"."LMS_ID"
JOIN "INTEGRACION"."RESULTADO_APRENDIZAJE" "RA" ON "ADR"."REA_ID" = "RA"."REA_ID"
WHERE "U"."FIC_ID" = curso::BIGINT AND "ADR"."CMP_ID" = competencia::BIGINT AND "ID_USUARIO_LMS" = userid
ORDER BY "USR_NOMBRE" ASC;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_resultap AS
SELECT * FROM tablaTemp;
END;
$$
LANGUAGE 'plpgsql';

--
-------------------FUNCION PARA OBTENER RESULTADOS POR RESULTADO DE APRENDIZAJE CON USUARIOS DE ZAJUNA--------------------

CREATE OR REPLACE FUNCTION obtenerResultadosRea(curso VARCHAR, competencia VARCHAR, rea_id BIGINT)
RETURNS VOID AS
$$
begin
RAISE NOTICE 'Inicio de la función';
-- Crear la tabla temporal
CREATE TEMPORARY TABLE IF NOT EXISTS tablaTempApr(
"LMS_ID" BIGINT,
"USR_NUM_DOC" VARCHAR,
"USR_NOMBRE" VARCHAR,
"USR_APELLIDO" VARCHAR,
"FIC_ID" VARCHAR,
"CMP_ID" BIGINT,
"REA_ID" BIGINT,
"REA_NOMBRE" VARCHAR,
"ADR_ID" BIGINT,
"ADR_EVALUACION_RESULTADO" VARCHAR,
"ESTADO_SINCRONIZACION" BIGINT
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempApr("LMS_ID", "USR_NUM_DOC", "USR_NOMBRE", "USR_APELLIDO", "FIC_ID", "CMP_ID", "REA_ID", "REA_NOMBRE", "ADR_ID", "ADR_EVALUACION_RESULTADO", "ESTADO_SINCRONIZACION")
SELECT
u.id AS LMS_ID,
u.username AS "USR_NUM_DOC",
u.firstname AS "USR_NOMBRE",
u.lastname AS "USR_APELLIDO",
mc.idnumber AS "FIC_ID",
adr."CMP_ID",
rap."REA_ID",
rap."REA_NOMBRE",
adr."ADR_ID",
adr."ADR_EVALUACION_RESULTADO",
adr."ESTADO_SINCRONIZACION"
FROM mdl_user u
JOIN mdl_user_enrolments ue ON ue.userid = u.id
JOIN mdl_enrol e ON e.id = ue.enrolid
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_course mc ON mc.id = e.courseid
JOIN mdl_role r ON r.id = ra.roleid
JOIN public."TABLA_NOTASXRA_CC" adr ON u.id = adr."LMS_ID"
JOIN public."RESULTADO_APRENDIZAJE" rap ON adr."REA_ID" = rap."REA_ID"
WHERE mc.idnumber = curso AND adr."CMP_ID" = competencia::BIGINT AND rap."REA_ID" = rea_id::BIGINT
ORDER BY u.firstname ASC;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_result AS
SELECT * FROM tablaTempApr;

END;
$$
LANGUAGE 'plpgsql';


--
-----------------FUNCION PARA OBTENER LOS RESULTADOSAP POR RESULTADO DE APRENDIZAJE CON CONSUMO TOTAL DE INTEGRACION_REPLICA-------------------
-----------NO SE USA ACTUALMENTE---------------

CREATE OR REPLACE FUNCTION "INTEGRACION".obtenerResultadosReaId(curso VARCHAR, competencia VARCHAR, rea_id VARCHAR)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE IF NOT EXISTS tablaTemp(
"ID_USUARIO_LMS" BIGINT,
"USR_NUM_DOC" VARCHAR,
"USR_NOMBRE" VARCHAR,
"USR_APELLIDO" VARCHAR,
"FIC_ID" BIGINT,
"CMP_ID" BIGINT,
"REA_ID" BIGINT,
"REA_NOMBRE" VARCHAR,
"ADR_ID" BIGINT,
"ADR_EVALUACION_RESULTADO" VARCHAR,
"ESTADO_SINCRONIZACION" BIGINT
) ON COMMIT DROP; -- Se eliminará al final de la transacción

-- Limpiar la tabla temporal si ya existe
TRUNCATE TABLE tablaTemp;

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTemp("ID_USUARIO_LMS", "USR_NUM_DOC", "USR_NOMBRE", "USR_APELLIDO", "FIC_ID", "CMP_ID", "REA_ID", "REA_NOMBRE", "ADR_ID", "ADR_EVALUACION_RESULTADO", "ESTADO_SINCRONIZACION")
SELECT
"U"."ID_USUARIO_LMS",
"U"."USR_NUM_DOC",
"U"."USR_NOMBRE",
"U"."USR_APELLIDO",
"U"."FIC_ID",
"ADR"."CMP_ID",
"RA"."REA_ID",
"RA"."REA_NOMBRE",
"ADR"."ADR_ID",
"ADR"."ADR_EVALUACION_RESULTADO",
"ADR"."ESTADO_SINCRONIZACION"
FROM "INTEGRACION"."USUARIO_LMS" "U"
JOIN "INTEGRACION"."TABLA_NOTASXRA_CC" "ADR" ON "U"."LMS_ID" = "ADR"."LMS_ID"
JOIN "INTEGRACION"."RESULTADO_APRENDIZAJE" "RA" ON "ADR"."REA_ID" = "RA"."REA_ID"
WHERE "U"."FIC_ID" = curso::BIGINT AND "ADR"."CMP_ID" = competencia::BIGINT AND "RA"."REA_ID" = rea_id::BIGINT
ORDER BY "USR_NOMBRE" ASC;

-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_resultrea AS
SELECT * FROM tablaTemp;
END;
$$
LANGUAGE 'plpgsql';

--
---------------------FUNCION PARA OBTENER LAS COMPETENCIAS CONSUMO TOTAL DE INTEGRACION_REPLICA-------------------------------

CREATE OR REPLACE FUNCTION "INTEGRACION".obtenerCompetencias(course VARCHAR)
RETURNS VOID AS
$$
BEGIN
-- Crear la tabla temporal
CREATE TEMPORARY TABLE tablaTempComp(
"CMP_ID" BIGINT,
"CMP_NOMBRE" VARCHAR,
"CMP_ACTIVO" VARCHAR,
"CMP_CODIGO" BIGINT,
"FIC_ID" BIGINT
);

-- Insertar los datos en la tabla temporal
INSERT INTO tablaTempComp("CMP_ID", "CMP_NOMBRE", "CMP_ACTIVO", "CMP_CODIGO", "FIC_ID")

select c2."CMP_ID", c2."CMP_NOMBRE", c2."CMP_ACTIVO", c2."CMP_CODIGO", fic."FIC_ID"
from "INTEGRACION"."V_FICHA_CARACTERIZACION_B" fic
join "INTEGRACION"."V_PROGRAMA_FORMACION_B" prf on fic."PRF_ID" = prf."PRF_ID"
join "INTEGRACION"."COMPETENCIAXPROGRAMA" c on c."PRF_ID" = prf."PRF_ID"
join "INTEGRACION"."COMPETENCIA" c2 on c2."CMP_ID" = c."CMP_ID"
WHERE fic."FIC_ID" = course::BIGINT AND c2."CMP_ACTIVO" = '1';


-- Crear la vista a partir de la tabla temporal
CREATE OR REPLACE VIEW vista_com AS
SELECT * FROM tablaTempComp;
END;
$$
LANGUAGE 'plpgsql';


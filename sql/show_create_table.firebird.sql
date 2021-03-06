-- noinspection SqlResolveForFile
-- @link https://stackoverflow.com/a/12074601

-- get the field descriptions

SELECT
    RF.RDB$FIELD_POSITION AS "id",
    TRIM(RF.RDB$FIELD_NAME) AS "name",
    (CASE F.RDB$FIELD_TYPE
     WHEN 7 THEN
         CASE F.RDB$FIELD_SUB_TYPE
         WHEN 0 THEN 'SMALLINT'
         WHEN 1 THEN 'NUMERIC(' || F.RDB$FIELD_PRECISION || ',' || (-F.RDB$FIELD_SCALE) || ')'
         WHEN 2 THEN 'DECIMAL(' || F.RDB$FIELD_PRECISION || ',' || (-F.RDB$FIELD_SCALE) || ')'
         END
     WHEN 8 THEN
         CASE F.RDB$FIELD_SUB_TYPE
         WHEN 0 THEN 'INTEGER'
         WHEN 1 THEN 'NUMERIC(' || F.RDB$FIELD_PRECISION || ',' || (-F.RDB$FIELD_SCALE) || ')'
         WHEN 2 THEN 'DECIMAL(' || F.RDB$FIELD_PRECISION || ',' || (-F.RDB$FIELD_SCALE) || ')'
         END
     WHEN 9 THEN 'QUAD'
     WHEN 10 THEN 'FLOAT'
     WHEN 12 THEN 'DATE'
     WHEN 13 THEN 'TIME'
     WHEN 14 THEN 'CHAR(' || (TRUNC(F.RDB$FIELD_LENGTH / CH.RDB$BYTES_PER_CHARACTER)) || ') '
     WHEN 16 THEN
         CASE F.RDB$FIELD_SUB_TYPE
         WHEN 0 THEN 'BIGINT'
         WHEN 1 THEN 'NUMERIC(' || F.RDB$FIELD_PRECISION || ', ' || (-F.RDB$FIELD_SCALE) || ')'
         WHEN 2 THEN 'DECIMAL(' || F.RDB$FIELD_PRECISION || ', ' || (-F.RDB$FIELD_SCALE) || ')'
         END
     WHEN 27 THEN 'DOUBLE'
     WHEN 35 THEN 'TIMESTAMP'
     WHEN 37 THEN 'VARCHAR(' || (TRUNC(F.RDB$FIELD_LENGTH / CH.RDB$BYTES_PER_CHARACTER)) || ')'
     WHEN 40 THEN 'CSTRING' || (TRUNC(F.RDB$FIELD_LENGTH / CH.RDB$BYTES_PER_CHARACTER)) || ')'
     WHEN 45 THEN 'BLOB_ID'
     WHEN 261 THEN 'BLOB SUB_TYPE ' || F.RDB$FIELD_SUB_TYPE
     ELSE 'RDB$FIELD_TYPE: ' || F.RDB$FIELD_TYPE || '?'
     END) AS "type",
    IIF(COALESCE(RF.RDB$NULL_FLAG, 0) = 0, NULL, 'NOT NULL') AS "isnull",
    COALESCE(RF.RDB$DEFAULT_SOURCE, F.RDB$DEFAULT_SOURCE) AS "default",
    TRIM(CH.RDB$CHARACTER_SET_NAME) AS "charset",
    TRIM(DCO.RDB$COLLATION_NAME) AS "collation",
    TRIM(F.RDB$VALIDATION_SOURCE) AS "check",
    TRIM(RF.RDB$DESCRIPTION) AS "desc"
FROM
    RDB$RELATION_FIELDS RF
    JOIN RDB$FIELDS F ON (F.RDB$FIELD_NAME = RF.RDB$FIELD_SOURCE)
    LEFT OUTER JOIN RDB$CHARACTER_SETS CH ON (CH.RDB$CHARACTER_SET_ID = F.RDB$CHARACTER_SET_ID)
    LEFT OUTER JOIN RDB$COLLATIONS DCO
        ON ((DCO.RDB$COLLATION_ID = F.RDB$COLLATION_ID) AND (DCO.RDB$CHARACTER_SET_ID = F.RDB$CHARACTER_SET_ID))
WHERE
    (RF.RDB$RELATION_NAME = ?) -- your table here
    AND (COALESCE(RF.RDB$SYSTEM_FLAG, 0) = 0)
ORDER BY
    RF.RDB$FIELD_POSITION;


-- get the indices

SELECT
  S.RDB$FIELD_POSITION AS "pos",
  S.RDB$FIELD_NAME AS "field",
  I.RDB$INDEX_ID AS "id",
  I.RDB$UNIQUE_FLAG AS "unique"
FROM
  RDB$INDEX_SEGMENTS AS S,
  RDB$INDICES AS I
WHERE
  S.RDB$INDEX_NAME = I.RDB$INDEX_NAME
  AND I.RDB$RELATION_NAME = ?; -- your table here

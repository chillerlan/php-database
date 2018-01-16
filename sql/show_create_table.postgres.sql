-- noinspection SqlResolveForFile
-- @link https://stackoverflow.com/a/16154183

SELECT
	a.attnum AS "id",
	a.attname AS "name",
	pg_catalog.format_type(a.atttypid, a.atttypmod) AS "type",
	CASE WHEN a.attnotnull = TRUE
		THEN 'NOT NULL'
	ELSE '' END AS "isnull",
	CASE WHEN (
		          SELECT substring(pg_catalog.pg_get_expr(d.adbin, d.adrelid) FOR 128)
		          FROM pg_catalog.pg_attrdef d
		          WHERE
			          d.adrelid = a.attrelid
			          AND d.adnum = a.attnum
			          AND a.atthasdef
	          ) IS NOT NULL
		THEN 'DEFAULT ' || (
			SELECT substring(pg_catalog.pg_get_expr(d.adbin, d.adrelid) FOR 128)
			FROM pg_catalog.pg_attrdef d
			WHERE
				d.adrelid = a.attrelid
				AND d.adnum = a.attnum
				AND a.atthasdef
		)
	ELSE '' END AS "default",
	(
		SELECT collation_name
		FROM information_schema.columns
		WHERE
			columns.table_name = b.relname
			AND columns.column_name = a.attname
	) AS "collation",
	(
		SELECT c.relname
		FROM pg_catalog.pg_class AS c, pg_attribute AS at, pg_catalog.pg_index AS i, pg_catalog.pg_class c2
		WHERE
			c.relkind = 'i'
			AND at.attrelid = c.oid
			AND i.indexrelid = c.oid
			AND i.indrelid = c2.oid
			AND c2.relname = b.relname
			AND at.attnum = a.attnum
	) AS "index"
FROM
	pg_catalog.pg_attribute AS a
	INNER JOIN
	(
		SELECT
			c.oid,
			n.nspname,
			c.relname
		FROM pg_catalog.pg_class AS c, pg_catalog.pg_namespace AS n
		WHERE
			pg_catalog.pg_table_is_visible(c.oid)
			AND n.oid = c.relnamespace
			AND c.relname = ? -- your table here
		ORDER BY 2, 3) b
		ON a.attrelid = b.oid
	INNER JOIN
	(
		SELECT a.attrelid
		FROM pg_catalog.pg_attribute a
		WHERE
			a.attnum > 0
			AND NOT a.attisdropped
		GROUP BY a.attrelid
	) AS e
		ON a.attrelid = e.attrelid
WHERE a.attnum > 0
      AND NOT a.attisdropped
ORDER BY a.attnum;

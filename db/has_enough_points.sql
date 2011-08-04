-- PARAMS user_id, points
CREATE OR REPLACE FUNCTION has_enough_points(INTEGER, INTEGER) RETURNS BOOLEAN AS
 $BODY$
DECLARE
	current_points INTEGER;
BEGIN
SELECT points INTO current_points FROM "USER" WHERE user_id = $1;
IF $2 > current_points THEN
	RETURN FALSE;
ELSE
	UPDATE "USER" SET points = points - $2 WHERE user_id = $1;
	RETURN TRUE;
END IF;
END;
$BODY$
LANGUAGE 'plpgsql';
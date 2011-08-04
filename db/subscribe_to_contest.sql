-- PARAMS contest et user
CREATE OR REPLACE FUNCTION subscribe_to_contest(INTEGER, INTEGER, INTEGER) RETURNS BOOLEAN AS
 $BODY$
BEGIN
	BEGIN
		INSERT INTO participe (contest_id, user_id, vote) 
			VALUES ($1, $2, 0);
		UPDATE "USER" SET points = points + $3 WHERE user_id = $2;
	EXCEPTION WHEN unique_violation THEN
		RETURN false;
	END;
RETURN true;	
END;
$BODY$
LANGUAGE 'plpgsql';
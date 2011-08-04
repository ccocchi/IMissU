-- PARAMS user et favorite
CREATE OR REPLACE FUNCTION add_favorite(INTEGER, INTEGER) RETURNS BOOLEAN AS
 $BODY$
BEGIN
	BEGIN
		INSERT INTO favorite (user_id, use_user_id) 
		VALUES ($1, $2);
	EXCEPTION WHEN unique_violation THEN
		RETURN false;
	END;
RETURN true;	
END;
$BODY$
LANGUAGE 'plpgsql';
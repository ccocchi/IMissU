-- PARAMS user et favorite
CREATE OR REPLACE FUNCTION add_photo(INTEGER, INTEGER) RETURNS INTEGER AS
 $BODY$
 DECLARE
	insert_id INTEGER;
BEGIN
	BEGIN
		INSERT INTO photo (user_id, validate, date) 
			VALUES ($1, 'true', now());
		insert_id = currval('photo_photo_id_seq');	
		UPDATE "USER" SET points = points + $2 WHERE user_id = $1;
	END;
RETURN insert_id;	
END;
$BODY$
LANGUAGE 'plpgsql';
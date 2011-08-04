-- PARAMS content, thread_id, sender et destinataire
CREATE OR REPLACE FUNCTION  add_message(TEXT, INTEGER, INTEGER, INTEGER, OUT res INTEGER) AS
 $BODY$
BEGIN
INSERT INTO message (thread_id, user_id, content, date) 
	VALUES ($2, $3, $1, now());
UPDATE thread_user SET read = 'false' WHERE thread_id = $2 AND user_id = $4;
UPDATE thread_user SET deleted = 'false' WHERE thread_id = $2;
UPDATE thread SET last_message = now() WHERE thread_id = $2;
END;
$BODY$
LANGUAGE 'plpgsql';
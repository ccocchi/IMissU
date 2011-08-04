-- PARAMS subject, createur et destinataire
CREATE OR REPLACE FUNCTION  create_thread(VARCHAR(255), INTEGER, INTEGER) RETURNS INTEGER AS
 $BODY$
DECLARE
	insert_id INTEGER;
BEGIN
INSERT INTO thread (user_id, use_user_id, subject) 
	VALUES ($2, $3, $1);
insert_id = currval('thread_thread_id_seq');
INSERT INTO thread_user (user_id, thread_id, read, deleted) VALUES ($2, insert_id, 'true', 'false');
INSERT INTO thread_user (user_id, thread_id, read, deleted) VALUES ($3, insert_id, 'true', 'false');
RETURN insert_id;
END;
$BODY$
LANGUAGE 'plpgsql';
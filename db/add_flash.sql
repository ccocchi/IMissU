-- PARAMS createur, destinataire, subject, content et value
CREATE OR REPLACE FUNCTION  add_flash(INTEGER, INTEGER, VARCHAR(255), TEXT, INTEGER) RETURNS BOOLEAN AS
 $BODY$
DECLARE
	insert_id INTEGER;
BEGIN
INSERT INTO vote (user_id, use_user_id, date, points)
	VALUES ($1, $2, now(), $5);
UPDATE "USER" SET vote = vote + $5 WHERE user_id = $2;
INSERT INTO thread (user_id, use_user_id, subject, last_message) 
	VALUES ($1, $2, $3, now());
insert_id = currval('thread_thread_id_seq');
INSERT INTO thread_user (user_id, thread_id, read, deleted) VALUES ($1, insert_id, 'true', 'true');
INSERT INTO thread_user (user_id, thread_id, read, deleted) VALUES ($2, insert_id, 'false', 'false');
INSERT INTO message (thread_id, user_id, content, date) 
	VALUES (insert_id, $1, $4, now());
RETURN TRUE;
END;
$BODY$
LANGUAGE 'plpgsql';
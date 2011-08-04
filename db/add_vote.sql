-- PARAMS createur, destinataire et value
CREATE OR REPLACE FUNCTION  add_vote(INTEGER, INTEGER, INTEGER) RETURNS BOOLEAN AS
 $BODY$
 BEGIN
INSERT INTO vote (user_id, use_user_id, date, points)
	VALUES ($1, $2, now(), $3);
UPDATE "USER" SET vote = vote + $3 WHERE user_id = $2;
RETURN TRUE;
END;
$BODY$
LANGUAGE 'plpgsql';
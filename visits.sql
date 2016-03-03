SELECT
  ss_user.first,
  ss_user.last,
  ss_user.email,
  group_concat(DISTINCT childgroup.name SEPARATOR ', '),
  group_concat(DISTINCT child.first SEPARATOR ', '),
  min(visit.created),
  max(visit.created),
  timediff(max(visit.created), min(visit.created)) AS duration
FROM visit
  LEFT JOIN ss_user ON visit.user_id = ss_user.id
  LEFT JOIN ss_user_group ON ss_user.id = ss_user_group.user_id
  LEFT JOIN ss_group ON ss_user_group.group_id = ss_group.id
  LEFT JOIN invite ON invite.user_id = ss_user.id
  LEFT JOIN ss_user child ON invite.invitee_id = child.id
  LEFT JOIN ss_user_group childug ON child.id = childug.user_id
  LEFT JOIN ss_group childgroup ON childug.group_id = childgroup.id
WHERE visit.user_id IS NOT NULL AND path LIKE '/home%'
GROUP BY visit.user_id, second(visit.created), minute(visit.created), hour(visit.created), day(visit.created),
  month(visit.created), year(visit.created)
ORDER BY visit.id DESC
INTO OUTFILE '/tmp/users9.csv' FIELDS TERMINATED BY ','
  ENCLOSED BY '"' LINES TERMINATED BY '\n';


SELECT
  ss_user.first,
  ss_user.last,
  ss_user.email,
  ss_group.name
FROM response visit
  LEFT JOIN ss_user ON visit.user_id = ss_user.id
  LEFT JOIN ss_user_group ON ss_user.id = ss_user_group.user_id
  LEFT JOIN ss_group ON ss_user_group.group_id = ss_group.id
ORDER BY visit.id DESC
INTO OUTFILE '/tmp/users11.csv' FIELDS TERMINATED BY ','
  ENCLOSED BY '"' LINES TERMINATED BY '\n';


SELECT
  ss_user.first,
  ss_user.last,
  ss_user.email,
  group_concat(DISTINCT ss_group.name SEPARATOR ', '),
  min(visit.created),
  max(visit.created),
  timediff(max(visit.created), min(visit.created)) AS duration
FROM response visit
  LEFT JOIN ss_user ON visit.user_id = ss_user.id
  LEFT JOIN ss_user_group ON ss_user.id = ss_user_group.user_id
  LEFT JOIN ss_group ON ss_user_group.group_id = ss_group.id
GROUP BY visit.user_id, hour(visit.created), day(visit.created), month(visit.created), year(visit.created)
ORDER BY visit.id DESC
INTO OUTFILE '/tmp/users13.csv' FIELDS TERMINATED BY ','
  ENCLOSED BY '"' LINES TERMINATED BY '\n';


SELECT
  ss_user.first,
  ss_user.last,
  ss_user.email
FROM visit
  LEFT JOIN ss_user ON visit.user_id = ss_user.id
  LEFT JOIN ss_user_group ON ss_user.id = ss_user_group.user_id
  LEFT JOIN ss_group ON ss_user_group.group_id = ss_group.id
WHERE path LIKE '/home%'
ORDER BY visit.id DESC
INTO OUTFILE '/tmp/users10.csv' FIELDS TERMINATED BY ','
  ENCLOSED BY '"' LINES TERMINATED BY '\n';


SELECT
  ss_user.first,
  ss_user.last,
  ss_user.email,
  group_concat(DISTINCT childgroup.name SEPARATOR ', '),
  group_concat(DISTINCT child.first SEPARATOR ', '),
  visit.created
FROM visit
  LEFT JOIN ss_user ON visit.user_id = ss_user.id
  LEFT JOIN ss_user_group ON ss_user.id = ss_user_group.user_id
  LEFT JOIN ss_group ON ss_user_group.group_id = ss_group.id
  LEFT JOIN invite ON invite.user_id = ss_user.id
  LEFT JOIN ss_user child ON invite.invitee_id = child.id
  LEFT JOIN ss_user_group childug ON child.id = childug.user_id
  LEFT JOIN ss_group childgroup ON childug.group_id = childgroup.id
WHERE path LIKE '/home%'
GROUP BY visit.user_id, hour(visit.created), day(visit.created), month(visit.created), year(visit.created)
ORDER BY visit.id DESC
INTO OUTFILE '/tmp/users13.csv' FIELDS TERMINATED BY ','
  ENCLOSED BY '"' LINES TERMINATED BY '\n';
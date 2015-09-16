/* copy users
INSERT IGNORE INTO ss_user(username, username_canonical, email, email_canonical, roles, created, enabled, locked, expired, credentials_expired, password, salt, first, last) VALUES ('bjcullinan', 'bjcullinan', 'admin@studysauce.com', 'admin@studysauce.com', 'a:1:{i:0;s:10:"ROLE_ADMIN";}', '2014-08-23 15:47:50', 1, 0, 0, 0, 'Q0pER0g2HhhsoGTttQXI16cxgvb7gM9bVPERG9Afiug', '$S$DYbMXewEA', 'Brian', 'Cullinan');
*/

SELECT concat(
           'INSERT IGNORE INTO ss_user(username, username_canonical, email, email_canonical, roles, created, ',
           'last_login, enabled, locked, expired, credentials_expired, password, salt, first, last)',
           ' VALUES (', quote(if(username IS NULL, '', username)), ',',
           quote(if(username_canonical IS NULL, '', username_canonical)), ',',
           quote(if(email IS NULL, '', email)), ',', quote(if(email_canonical IS NULL, '', email_canonical)),
           ',', if(roles IS NULL, 'null', quote(roles)), ',\'', created, '\',',
           if(last_login IS NULL, 'null', concat('\'', last_login, '\'')), ',',
           enabled, ',', locked, ',', expired, ',', credentials_expired, ',', quote(if(password IS NULL, '', password)),
           ',',
           quote(if(salt IS NULL, '', salt)), ',', replace(quote(if(first IS NULL, '', first)), '\\\'', '\'\''), ',',
           replace(quote(if(last IS NULL, '', last)), '\\\'', '\'\''), ');')
  AS '/* insert */'
FROM (
       SELECT
         name                                                         AS username,
         name                                                         AS username_canonical,
         mail                                                         AS email,
         lower(mail)                                                  AS email_canonical,
         /* convert to test data
         lower(concat(replace(mail, '@', '_'), '@example.org'))              AS email_canonical,
         */
         IF((SELECT count(*)
             FROM studysauce.users_roles, studysauce.role
             WHERE users_roles.uid = users.uid
                   AND role.rid = users_roles.rid
                   AND role.name = 'adviser') > 0,
            'a:1:{i:0;s:12:"ROLE_ADVISER";}',
            IF((SELECT count(*)
                FROM studysauce.users_roles, studysauce.role
                WHERE users_roles.uid = users.uid
                      AND role.rid = users_roles.rid
                      AND role.name = 'master adviser') > 0,
               'a:1:{i:0;s:12:"ROLE_ADVISER";}',
               IF((SELECT count(*)
                   FROM studysauce.users_roles, studysauce.role
                   WHERE users_roles.uid = users.uid
                         AND role.rid = users_roles.rid
                         AND role.name = 'parent') > 0,
                  'a:1:{i:0;s:11:"ROLE_PARENT";}',
                  IF((SELECT count(*)
                      FROM studysauce.users_roles, studysauce.role
                      WHERE users_roles.uid = users.uid
                            AND role.rid = users_roles.rid
                            AND role.name = 'partner') > 0,
                     'a:1:{i:0;s:12:"ROLE_PARTNER";}',
                     IF((SELECT count(*)
                         FROM studysauce.users_roles, studysauce.role
                         WHERE users_roles.uid = users.uid
                               AND role.rid = users_roles.rid
                               AND role.name = 'administrator') > 0,
                        'a:1:{i:0;s:10:"ROLE_ADMIN";}', 'a:0:{}'))))) AS roles,
         FROM_UNIXTIME(created)                                       AS created,
         FROM_UNIXTIME(access)                                        AS last_login,
         1                                                            AS enabled,
         0                                                            AS locked,
         0                                                            AS expired,
         0                                                            AS credentials_expired,
         SUBSTR(pass FROM 13) as password,
         SUBSTR(pass FROM 1 FOR 12) as salt,
         /* convert to test data
         'pKmDp4jBETNsSr4z3HIAL8ttTS08ZQ6YPfzyy3F0HNR'                AS password,
         '$S$DB1qzKYe9'                                               AS salt,
         */
         field_first_name_value                                       AS first,
         field_last_name_value                                        AS last
       FROM studysauce.users
         LEFT JOIN studysauce.field_data_field_first_name fn
           ON fn.entity_id = uid AND fn.entity_type = 'user'
         LEFT JOIN studysauce.field_data_field_last_name ln
           ON ln.entity_id = uid AND ln.entity_type = 'user'
     ) AS users

UNION
/* copy groups
INSERT IGNORE INTO ss_user_group(user_id, group_id) VALUES ((SELECT id from ss_user where email = 'marketing@studysauce.com'), (SELECT id from ss_group where name = 'Stephen''s list'));
*/

SELECT concat('INSERT IGNORE INTO ss_group(name, description, created, roles) VALUES (',
              replace(quote(title), '\\\'', '\'\''), ',\'\',', '\'', from_unixtime(created),
              '\',\'a:1:{i:0;s:9:"ROLE_PAID";}\');')
  AS '/* insert */'
FROM (
       SELECT
         title,
         created
       FROM studysauce.node
       WHERE type = 'adviser_membership'
     ) AS groups

UNION

SELECT concat('INSERT IGNORE INTO ss_user_group(user_id, group_id) VALUES ((SELECT id from ss_user where email = ',
              quote(if(mail IS NULL, '', mail)), '),(SELECT id from ss_group where name = ',
              replace(quote(if(title IS NULL, '', title)), '\\\'', '\'\''), '));')
  AS '/* insert */'
FROM (
       SELECT
         mail,
         title
       FROM studysauce.og_membership
         LEFT JOIN studysauce.users
           ON uid = etid AND entity_type = 'user'
         LEFT JOIN studysauce.node
           ON nid = gid AND group_type = 'node'
     ) AS user_groups

UNION
/* copy schedules
INSERT IGNORE INTO ss_user_group(user_id, group_id) VALUES ((SELECT id from ss_user where email = 'marketing@studysauce.com'), (SELECT id from ss_group where name = 'Stephen''s list'));
*/

SELECT concat('INSERT IGNORE INTO schedule(user_id, university, grades, weekends, sharp6am11am,',
              ' sharp11am4pm, sharp4pm9pm, sharp9pm2am, created) VALUES ((SELECT id from ss_user ',
              'where email = ', quote(if(title IS NULL, '', title)), '),',
              quote(if(university IS NULL, '', university)), ',', quote(if(grades IS NULL, '', grades)), ',',
              quote(if(weekends IS NULL, '', weekends)),
              ',', if(sharp6am11am IS NULL, 'null', sharp6am11am), ',', if(sharp11am4pm IS NULL, 'null', sharp11am4pm),
              ',',
              if(sharp4pm9pm IS NULL, 'null', sharp4pm9pm), ',', if(sharp9pm2am IS NULL, 'null', sharp9pm2am), ',\'',
              created, '\');')
  AS '/* insert */'
FROM (
       SELECT
         title,
         field_university_value                  AS university,
         replace(field_grades_value, '_', '-')   AS grades,
         replace(field_weekends_value, '_', '-') AS weekends,
         field_6_am_11_am_value                  AS sharp6am11am,
         field_11_am_4_pm_value                  AS sharp11am4pm,
         field_4_pm_9_pm_value                   AS sharp4pm9pm,
         field_9_pm_2_am_value                   AS sharp9pm2am,
         from_unixtime(created)                  AS created
       FROM studysauce.node
         LEFT JOIN studysauce.field_data_field_university
           ON field_data_field_university.entity_id = node.nid
         LEFT JOIN studysauce.field_data_field_grades
           ON field_data_field_grades.entity_id = node.nid
         LEFT JOIN studysauce.field_data_field_weekends
           ON field_data_field_weekends.entity_id = node.nid
         LEFT JOIN studysauce.field_data_field_6_am_11_am
           ON field_data_field_6_am_11_am.entity_id = node.nid
         LEFT JOIN studysauce.field_data_field_11_am_4_pm
           ON field_data_field_11_am_4_pm.entity_id = node.nid
         LEFT JOIN studysauce.field_data_field_4_pm_9_pm
           ON field_data_field_4_pm_9_pm.entity_id = node.nid
         LEFT JOIN studysauce.field_data_field_9_pm_2_am
           ON field_data_field_9_pm_2_am.entity_id = node.nid
       WHERE type = 'schedule'
     ) AS schedules

UNION
/* copy courses */
SELECT
  concat('INSERT IGNORE INTO course(schedule_id, name, type, study_type, study_difficulty, start_time, end_time,',
         ' created, deleted, dotw) VALUES ((SELECT schedule.id from ss_user,schedule where ss_user.id = schedule.user_id ',
         ' and email = ', quote(if(title IS NULL, '', title)), '),', replace(quote(name), '\\\'', '\'\''), ',',
         quote(type), ',', quote(if(study_type IS NULL, '', study_type)),
         ',', quote(if(study_difficulty IS NULL, '', study_difficulty)), ',\'', start_time, '\',\'', end_time, '\',\'',
         now(), '\',0,\'', dotw, '\');')
    AS '/* insert */'
FROM (
       SELECT
         title,
         field_class_name_value                                                               AS name,
         if(field_event_type_value IS NULL, 'c', field_event_type_value)                      AS type,
         field_study_type_value                                                               AS study_type,
         field_study_difficulty_value                                                         AS study_difficulty,
         date_sub(field_time_value, INTERVAL 7 HOUR)                                          AS start_time,
         date_sub(field_time_value2, INTERVAL 7 HOUR)                                         AS end_time,
         replace(group_concat(field_day_of_the_week_value SEPARATOR ','), 'weekly', 'Weekly') AS dotw
       FROM studysauce.field_data_field_classes
         LEFT JOIN studysauce.node
           ON nid = entity_id
         LEFT JOIN studysauce.field_data_field_class_name
           ON field_data_field_class_name.entity_id = field_data_field_classes.field_classes_value
         LEFT JOIN studysauce.field_data_field_event_type
           ON field_data_field_event_type.entity_id = field_data_field_classes.field_classes_value
         LEFT JOIN studysauce.field_data_field_study_type
           ON field_data_field_study_type.entity_id = field_data_field_classes.field_classes_value
         LEFT JOIN studysauce.field_data_field_study_difficulty
           ON field_data_field_study_difficulty.entity_id = field_data_field_classes.field_classes_value
         LEFT JOIN studysauce.field_data_field_time
           ON field_data_field_time.entity_id = field_data_field_classes.field_classes_value
         LEFT JOIN studysauce.field_data_field_day_of_the_week
           ON field_data_field_day_of_the_week.entity_id = field_data_field_classes.field_classes_value
       WHERE (field_event_type_value = 'c' OR field_event_type_value = 'o' OR field_event_type_value IS NULL)
             AND trim(field_class_name_value) != '' AND field_time_value IS NOT NULL AND field_time_value2 IS NOT NULL
       GROUP BY field_data_field_day_of_the_week.entity_id
     ) AS courses
WHERE dotw IS NOT NULL

UNION

/* copy checkins */
SELECT concat(
           'INSERT IGNORE INTO checkin(course_id,checkin,utc_checkin,checkout,utc_checkout) VALUES ((',
           'SELECT course.id from ss_user,schedule,course where ss_user.id = schedule.user_id ',
           ' and email = ', quote(if(title IS NULL, '', title)),
           ' and course.schedule_id = schedule.id and course.name = ',
           quote(course), '),\'', if(checkin IS NULL, 'null', checkin), '\',\'', if(utc_checkin IS NULL, 'null', utc_checkin),
           '\',',
           if(checkout IS NULL, 'null', concat('\'',checkout,'\'')), ',', if(utc_checkout IS NULL, 'null', concat('\'',utc_checkout,'\'')), ');')
  AS '/* insert */'
FROM (
       SELECT
         title,
         field_class_name_value AS course,
         field_checkin_value    AS checkin,
         field_checkin_value2   AS checkout,
         now()                  AS utc_checkin,
         now()                  AS utc_checkout
       FROM studysauce.field_data_field_checkin
         LEFT JOIN studysauce.field_data_field_classes
           ON field_data_field_checkin.entity_id = field_classes_value
         LEFT JOIN studysauce.node
           ON nid = field_data_field_classes.entity_id
         LEFT JOIN studysauce.field_data_field_class_name
           ON field_data_field_class_name.entity_id = field_classes_value
       WHERE field_class_name_value IS NOT NULL
     ) AS checkins
where checkin is not null

UNION
/* copy goals */
SELECT
  concat('INSERT IGNORE INTO goal(user_id, type, goal, reward, created) VALUES ((SELECT id from ss_user where email = ',
         quote(if(title IS NULL, '', title)), '),', replace(quote(type), '\\\'', '\'\''), ',',
         quote(goal), ',', replace(quote(if(reward IS NULL, '', reward)), '\\\'', '\'\''),',\'',created,'\');')
    AS '/* insert */'
FROM (
       SELECT
         title,
         if(field_type_value = 'outcome',field_gpa_value,
            if(field_type_value = 'milestone',field_grade_value,
               if(field_type_value = 'behavior',field_hours_value,''))) as goal,
         field_type_value                                                                     AS type,
         field_reward_value as reward,
         FROM_UNIXTIME(created) as created
       FROM studysauce.field_data_field_goals
         LEFT JOIN studysauce.node
           ON nid = entity_id
         LEFT JOIN studysauce.field_data_field_type
           ON field_data_field_type.entity_id = field_goals_value
         LEFT JOIN studysauce.field_data_field_reward
           ON field_data_field_reward.entity_id = field_goals_value
         LEFT JOIN studysauce.field_data_field_gpa
           ON field_data_field_gpa.entity_id = field_goals_value
         LEFT JOIN studysauce.field_data_field_grade
           ON field_data_field_grade.entity_id = field_goals_value
         LEFT JOIN studysauce.field_data_field_hours
           ON field_data_field_hours.entity_id = field_goals_value
     ) AS goals
WHERE title IS NOT NULL


UNION
/* copy deadlines */
SELECT
  concat('INSERT IGNORE INTO deadline(user_id, course_id, assignment, due_date, percent, completed, created, deleted, reminder, reminder_sent)',
         ' VALUES ((SELECT id from ss_user where email = ', quote(if(title IS NULL, '', title)), '),',
         '(SELECT id from course where name = ',quote(if(course IS NULL, '', course)),'),',replace(quote(assignment), '\\\'', '\'\''), ',',
         quote(due_date), ',', quote(if(percent IS NULL, 'null', percent)),',0,\'',created,'\',0,\'',reminder,'\',\'',if(reminder_sent IS NULL,'null',reminder_sent),'\');')
    AS '/* insert */'
FROM (
       SELECT
         title,
         field_class_name_value as course,
         field_assignment_value as assignment,
         field_due_date_value as due_date,
         field_percent_value percent,
         group_concat(field_reminder_value SEPARATOR ',') as reminder,
         group_concat(field_reminder_sent_value SEPARATOR ',') as reminder_sent,
         FROM_UNIXTIME(created) as created
       FROM studysauce.field_data_field_reminders
         LEFT JOIN studysauce.node
           ON nid = entity_id
         LEFT JOIN studysauce.field_data_field_class_name
           ON field_data_field_class_name.entity_id = field_reminders_value
         LEFT JOIN studysauce.field_data_field_assignment
           ON field_data_field_assignment.entity_id = field_reminders_value
         LEFT JOIN studysauce.field_data_field_due_date
           ON field_data_field_due_date.entity_id = field_reminders_value
         LEFT JOIN studysauce.field_data_field_percent
           ON field_data_field_percent.entity_id = field_reminders_value
         LEFT JOIN studysauce.field_data_field_reminder
           ON field_data_field_reminder.entity_id = field_reminders_value
         LEFT JOIN studysauce.field_data_field_reminder_sent
           ON field_data_field_reminder_sent.entity_id = field_reminders_value
       GROUP BY field_data_field_reminder.entity_id,field_data_field_reminder_sent.entity_id
     ) AS deadlines
WHERE course IS NOT NULL

UNION
/* copy partner and parent invites */
SELECT
  concat('INSERT IGNORE INTO partner_invite(user_id, partner_id, first, last, email, activated, code, created, reminder, permissions)',
         ' VALUES ((SELECT id from ss_user where email = ', quote(if(mail IS NULL, '', mail)), '),',
         '(SELECT id from ss_user where email = ', quote(if(email IS NULL, '', email)), '),',
         replace(quote(first), '\\\'', '\'\''), ',',replace(quote(last), '\\\'', '\'\''), ',',
         replace(quote(email), '\\\'', '\'\''), ',',activated,',',replace(quote(code), '\\\'', '\'\''), ',',
         if(created IS NULL,'null',concat('\'',created,'\'')),',',if(reminder IS NULL,'null',concat('\'',reminder,'\'')),',',if(permissions IS NULL,'null',concat('\'',permissions,'\'')),');')
  AS '/* insert */'
FROM (
       SELECT
         mail,
         field_first_name_value as first,
         field_last_name_value as last,
         field_email_value as email,
         field_activated_value as activated,
         field_code_value as code,
         if(field_sent_value is null,from_unixtime(created),field_sent_value) as created,
         field_reminder_value as reminder,
         group_concat(field_permissions_value SEPARATOR ',') as permissions
       FROM studysauce.field_data_field_partners
         LEFT JOIN studysauce.users
           ON uid = entity_id
         left join studysauce.field_data_field_first_name
           on field_data_field_first_name.entity_id = field_partners_value
         left join studysauce.field_data_field_last_name
           on field_data_field_last_name.entity_id = field_partners_value
         left join studysauce.field_data_field_email
           on field_data_field_email.entity_id = field_partners_value
         left join studysauce.field_data_field_activated
           on field_data_field_activated.entity_id = field_partners_value
         left join studysauce.field_data_field_code
           on field_data_field_code.entity_id = field_partners_value
         left join studysauce.field_data_field_sent
           on field_data_field_sent.entity_id = field_partners_value
         left join studysauce.field_data_field_reminder
           on field_data_field_reminder.entity_id = field_partners_value
         left join studysauce.field_data_field_permissions
           on field_data_field_permissions.entity_id = field_partners_value
       where field_email_value is not null
       GROUP BY field_data_field_permissions.entity_id
     ) AS partners




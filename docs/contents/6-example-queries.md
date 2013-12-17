
Example Queries
===============

This section contains example queries
to show an example of what kind of information we can extract from the application's database
using SQL queries.
These queries are in MySQL's SQL Dialect.

Please note that,
in order to protect the user's privacy,
most of these queries are not used in the actual application,
but it demonstrates how a normalized database
can enable us to query and gain insights from our database.


Example 1
---------

Find the names of the instructors that teaches in my semester (my schedule's ID = 30).

```sql
SELECT DISTINCT name FROM instructors
WHERE group_id IN (
  SELECT id FROM groups
  WHERE selected AND course_id IN (
    SELECT id FROM courses
    WHERE schedule_id = 30))
ORDER BY name
```

+--------+
| `name` |
+========+
| Anan |
+------+
| Hutchatai |
+------+
| Siriporn |
+------+
| Somnuk |
+------+



Example 2
---------

Find the total hours of class per week that SKE11 students are taking (SKE11 schedule's ID = 31).

```sql
SELECT
  SUM(finish_time - start_time) / 60 AS hours_per_week
FROM periods
WHERE group_id IN (
  SELECT id FROM groups
  WHERE selected AND course_id IN (
    SELECT id FROM courses
    WHERE schedule_id = 31))
```

+------------------+
| `hours_per_week` |
+==================+
| 23.0000 |
+------+


Example 3
---------

Remove the cached timetable that is retrieved from KU
that is older than 1 hour.

During the registration period,
the timetable in KU's registration system are changed often,
so we can't keep our cache of timetables for too long.

The system will run this query occasionally
to clean the old timetables (that are older than 1 hour).



```sql
DELETE FROM ku_timetables
WHERE fetched_at < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 HOUR)
```



Example 4
---------

Find the name and the URL of the latest schedule of each user.

```sql
SELECT
  CONCAT("http://math.random.fi/schedule/view.php?id=",
    s.id, s.secret)  AS url,
  s.name             AS schedule_name,
  u.name             AS user_name
FROM `schedules`     AS s
INNER JOIN users     AS u
  ON (u.id = s.user_id)
WHERE s.id = (
  SELECT MAX(id) FROM schedules AS s2
  WHERE s2.user_id = s.user_id)
```

| `url` | `schedule_name` | `user_name` |
| ----- | --------------- | ----------- |
| `.../view.php?id=41807239` | SKE09 Y3T1 Schedule | Thai Pangsakulyanont |
| `.../view.php?id=40b96449` | Beam_Magic's SKE09 Y3T1 | Beammagic Goldenfish |
| `.../view.php?id=4336448a` | Universez | Sippakorn Widsankun |
| `.../view.php?id=4428fe5a` | 3-2 | Thitima Tamoharanawong |


Example 5
---------

Select the name of the schedule,
name of the owner of the schedule,
and the total number of credits in that schedule.

```sql
SELECT s.name AS schedule_name,
       u.name AS user_name,
       SUM(IF(g.type = "Lecture", c.credits_lecture, c.credits_lab))
              AS credits
FROM       groups    AS g
INNER JOIN courses   AS c ON (c.id = g.course_id)
INNER JOIN schedules AS s ON (s.id = c.schedule_id)
LEFT  JOIN users AS u ON (u.id = s.user_id)
WHERE g.selected
GROUP BY c.schedule_id
```

| `schedule_name` | `user_name` | `credits` |
| --------------- | ----------- | ---------:|
| Thai's Schedule | Thai Pangsakulyanont | 13 |
| SKE11's Schedule | Thai Pangsakulyanont | 18 |
| Beam_Magic's SKE09 Y3T1 | Beammagic Goldenfish | 15 |
| SKE09 Y3T1 Schedule | Thai Pangsakulyanont | 27 |
| Test Schedule | _NULL_ | 3 |
| Universez | Sippakorn Widsankun | 12 |
| 3-2 | Thitima Tamoharanawong | 15 |


Example 6
---------

Find the ID and name of the schedule,
along with the owner's name,
that has selected a database course in the schedule.

```sql
SELECT u.name AS user_name,
       s.id   AS schedule_id,
       s.name AS schedule_name
FROM   schedules AS s
INNER JOIN users AS u ON (u.id = s.user_id)
WHERE "01219331" IN (
  SELECT c.course_id
  FROM   courses AS c
  INNER JOIN groups AS g ON (g.course_id = c.id)
  WHERE c.schedule_id = s.id AND g.selected)
```

| `user_name` | `schedule_id` | `schedule_name` |
| ----------- | -------------:| --------------- |
| Thai Pangsakulyanont | 30 | Thai's Schedule |
| Thai Pangsakulyanont | 41 | SKE09 Y3T1 Schedule |
| Beammagic Goldenfish | 40 | Beam_Magic's SKE09 Y3T1 |



Example 7
---------

List all users and number of schedules that contains a period in Wednesday,
sorted by the number of schedules descending.

```sql
SELECT u.name, COUNT(DISTINCT s.id) AS number_of_schedule
FROM users  AS u
LEFT JOIN (
  schedules AS s
  INNER JOIN courses AS c ON (c.schedule_id = s.id)
  INNER JOIN groups  AS g ON (g.course_id = c.id AND g.selected = 1)
  INNER JOIN periods AS p ON (p.group_id = g.id  AND p.day = 3)
) ON (s.user_id = u.id)
GROUP BY u.id
ORDER BY number_of_schedule DESC
```

| `name` | `number_of_schedule` |
| ------ | --------------------:|
| Thai Pangsakulyanont | 2 |
| Sippakorn Widsankun | 1 |
| Pitt Vejsuwan | 1 |
| Thitima Tamoharanawong | 1 |
| Nichy Han | 1 |
| Beammagic Goldenfish | 0 |




Example 8
---------

List the names of all instructors who teaches 01355112 _Foundation English II_
that we have information in our database,
sorted by name

```sql
SELECT DISTINCT i.name
FROM courses AS c
INNER JOIN groups      AS g ON (c.id = g.course_id)
INNER JOIN instructors AS i ON (g.id = i.group_id)
WHERE c.course_id = "01355112"
ORDER BY i.name
```

> _Anek, Anthony, Apiradi, Boonthong, Chariti, Chirapa, Jarinthon, Kamolwan,
> Krittaya, Marissa, Michael, Mingkwan, Montira, Montri, Napasri, Natnan,
> Nattakarn, Nongnuch, Numthip, Ong-Orn, Pacharaprapa, Panida, Panjanit,
> Pattra, Peangduen, Penwipa, Piwat, Pongampai, Pongsatorn, Prasith,
> Premrudee, Puntip, Rapin, Rasada, Richard James, Sarinsuk, Savika, Sirikul,
> Sumalee, Sunantha, Suwan, Swit, Tharini, Wani, Wannasiri, Wipada, Wipakorn_.





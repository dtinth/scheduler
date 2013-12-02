
Example Queries
===============

This section contains example queries
to show an example of what kind of information we can extract from the application's database
using SQL queries.

Please note that these queries are in MySQL's SQL Dialect.


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





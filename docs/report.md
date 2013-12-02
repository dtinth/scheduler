
Background and Opportunity
==========================

Each semester,
we need to plan on which courses to register in the next semester.
This process is very cumbersome,
because we need to find the list of all the subjects we need to register,
then find the timetable for each of the sections that we can enroll,
not to mention that when finding the timetable,
we had to know the subject ID;
we can't search by the subject name.

After gathering all the raw data,
we have to plan the courses
by drawing a timetable ourselves,
in order to see whether any courses have overlapping schedule or not.
Some other times,
the course schedule for some course are changed,
and so the students need to update their timetables again.

This opens an opportunity for us to create a solution
for this problem—a study scheduling tool that will help alleviate all these problems.


Objectives and Goals
====================

Our goal is to create:

* an easy-to-use study schedule planning application
* for KU students, as well as students outside of KU
* that can be used in the real world, outside of this database course.

For the initial version,
our software will have the following characteristics:

* User can create their own study timetable.
* User can save the timetable. The timetable will be saved to the database.
* For KU students, they can import the timetable from the registration website.
* System can create interesting report (which data will be anonymous).


Benefits
========

The benefits are obvious: College students, especially KU students,
will be able to use this tool to

* create and share a study timetable; and
* plan their study schedule more effectively.


Software Tools
==============

| Tool | Purpose |
| ---- | ------- |
| draw.io | ER Diagramming |
| MySQL   | Database Management System |
| PHP     | Scripting language on the server |
| phpMyAdmin | Database development and administration tool |
| AngularJS | Client-side application framework |
| DomCrawler | Scraping the registration website's timetable |
| Facebook PHP SDK | User Authentication and Login with Facebook |
| pdfLaTeX<br>Pygments<br>Redcloth<br>Pandoc | Report authoring tools |



ER Diagram
==========

![ER Diagram](er-diagram.png)



Development
===========

Naming Conventions
------------------

An entity set in the ER diagram corresponds to a table in the database.
However, we slightly alter the table name from the ER diagram:

| ER Entity Set Name | Database Table Name |
| ------------------ | ------------------- |
| Capitalized        | lowercased          |
| CamelCased         | snake_cased         |
| Singular           | Plural              |
| _Example_: `KUTimetable` | _Example_: `ku_timetables` |


Data Dictionary
---------------

TBA



Example Queries
===============

This section contains example queries
to show what kind of information we can extract from the application's database
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

| `name` |
| ---- |
| Anan |
| Hutchatai |
| Siriporn |
| Somnuk |


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

| `hours_per_week` |
| -------------- |
| \\(23.0000\\) |


Example 3
---------

Remove the cached timetable that is retrieved from KU
that is older than 1 hour.

```sql
DELETE FROM ku_timetables
WHERE fetched_at < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 HOUR)
```























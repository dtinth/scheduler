
Development
===========

Naming Conventions
------------------

An entity set in the ER diagram corresponds to a table in the database.
However, we slightly alter the table name from the ER diagram,
following the conventions of the Rails framework[^1].

| ER Entity Set Name | Database Table Name |
| ------------------ | ------------------- |
| Capitalized        | lowercased          |
| CamelCased         | snake_cased         |
| Singular           | Plural              |
| _Example_: `KUTimetable` | _Example_: `ku_timetables` |

[^1]: <http://itsignals.cascadia.com.au/?p=7>

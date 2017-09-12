# DBView
A little experiment of a database viewer in just one file

# The Views
1. Splash

Is used to get the needed information to establish a connection to a database (host, user, pass)

--> saves these information in session if correct, if not show error

2. DatabaseList

Displays all Database Names that are found with the SQL-Query `SHOW DATABASES`

3. TableList

Table specified by GET-Param `?db=TABLE_NAME` - displays all Table Names and the row count of these tables, may need some time to load if the table is too big because the row count gets resolved by querying `SELECT COUNT(*)` on the Table.

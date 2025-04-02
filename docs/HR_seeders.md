# all seeded data for HR

## users

UserSeeder.php  

<table>
    <tr>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Phone Number</th>
        <th>Birthdate</th>
        <th>Email</th>
        <th>Password</th>
        <th>Address_id</th>
    </tr>
    <tr>
        <td>Admin</td>
        <td>Admin</td>
        <td>0000000000</td>
        <td>1990-01-01</td>
        <td>Admin@example.com</td>
        <td>Admin</td>
        <td>1</td>
    </tr>
    <tr>
        <td>Fenno</td>
        <td>Feremans</td>
        <td>0324567891</td>
        <td>1990-01-01</td>
        <td>Fenno@bluesky.com</td>
        <td>password123</td>
        <td>2</td>
    </tr>
    <tr>
        <td>Jordi</td>
        <td>Schoetens</td>
        <td>0324558641</td>
        <td>1990-01-01</td>
        <td>Jordi@bluesky.com</td>
        <td>password123</td>
        <td>3</td>
    </tr>
    <tr>
        <td>HR</td>
        <td>HR</td>
        <td>0324767681</td>
        <td>1990-01-01</td>
        <td>HR@bluesky.com</td>
        <td>password123</td>
        <td>4</td>
    </tr>
    <tr>
        <td>Courier</td>
        <td>Courier</td>
        <td>0324142341</td>
        <td>1990-01-01</td>
        <td>Courier@bluesky.com</td>
        <td>password123</td>
        <td>5</td>
    </tr>
    <tr>
        <td>Airport</td>
        <td>Airport</td>
        <td>0324787653</td>
        <td>1990-01-01</td>
        <td>Airport@bluesky.com</td>
        <td>password123</td>
        <td>5</td>
    </tr>
    <tr>
        <td>DC</td>
        <td>DC</td>
        <td>0324795874</td>
        <td>1990-01-01</td>
        <td>DC@bluesky.com</td>
        <td>password123</td>
        <td>6</td>
    </tr>
    <tr>
        <td>Pickup</td>
        <td>Pickup</td>
        <td>0324798943</td>
        <td>1990-01-01</td>
        <td>Pickup@bluesky.com</td>
        <td>password123</td>
        <td>7</td>
    </tr>

</table>

## employees

EmployeesSeeder.php

<table>
    <tr>
        <th>Leave_balance</th>
        <th>User_id</th>
        <th>Team_id</th>
    </tr>
    <tr>
        <td>20</td>
        <td>2</td>
        <td>1</td>
    </tr>
    <tr>
        <td>20</td>
        <td>3</td>
        <td>1</td>
    </tr>
    <tr>
        <td>18</td>
        <td>4</td>
        <td>1</td>
    </tr>
    <tr>
        <td>15</td>
        <td>5</td>
        <td>2</td>
    </tr>
    <tr>
        <td>10</td>
        <td>6</td>
        <td>3</td>
    </tr>
    <tr>
        <td>13</td>
        <td>7</td>
        <td>4</td>
    </tr>
    <tr>
        <td>17</td>
        <td>8</td>
        <td>5</td>
    </tr>

</table>

## contracts

ContractsSeeder.php

<table>
    <tr>
        <th>Employee_id</th>
        <th>Job_id</th>
        <th>Start_date</th>
        <th>End_date</th>
    </tr>
    <tr>
        <td>2</td>
        <td>1</td>
        <td>2024-01-01</td>
        <td>NULL</td>
    </tr>
    <tr>
        <td>3</td>
        <td>1</td>
        <td>2024-01-01</td>
        <td>NULL</td>
    </tr>
    <tr>
        <td>4</td>
        <td>2</td>
        <td>2024-01-01</td>
        <td>NULL</td>
    </tr>
    <tr>
        <td>5</td>
        <td>3</td>
        <td>2024-01-01</td>
        <td>NULL</td>
    </tr>
    <tr>
        <td>6</td>
        <td>4</td>
        <td>2024-01-01</td>
        <td>NULL</td>
    </tr>
    <tr>
        <td>7</td>
        <td>5</td>
        <td>2024-01-01</td>
        <td>NULL</td>
    </tr>
    <tr>
        <td>8</td>
        <td>6</td>
        <td>2024-01-01</td>
        <td>NULL</td>
    </tr>

</table>

## teams

TeamsSeeder.php

<table>
    <tr>
        <th>Department</th>
        <th>manager_id</th>
    </tr>
    <tr>
        <td>HR</td>
        <td>3</td>
    </tr>
    <tr>
        <td>Courier</td>
        <td>5</td>
    </tr>
    <tr>
        <td>Airport</td>
        <td>6</td>
    </tr>
    <tr>
        <td>DC</td>
        <td>7</td>
    </tr>
    <tr>
        <td>Pickup</td>
        <td>8</td>
    </tr>
</table>

## functions

FunctionsSeeder.php

<table>
    <tr>
        <th>Name</th>
        <th>Role</th>
        <th>Description</th>
        <th>Salary_min</th>
        <th>Salary_max</th>
    </tr>
    <tr>
        <td>HR Manager</td>
        <td>HRManager</td>
        <td>Manager for HR</td>
        <td>3500.00</td>
        <td>4000.00</td>
    </tr>
    <tr>
        <td>HR Employee</td>
        <td>HR</td>
        <td>Employee for HR</td>
        <td>2500.00</td>
        <td>3000.00</td>
    </tr>
    <tr>
        <td>Courier</td>
        <td>courier</td>
        <td>courier employee</td>
        <td>2000.00</td>
        <td>2500.00</td>
    </tr>
    <tr>
        <td>Airport</td>
        <td>airport</td>
        <td>airport employee</td>
        <td>3000.00</td>
        <td>3500.00</td>
    </tr>
    <tr>
        <td>DC</td>
        <td>dc</td>
        <td>dc employee</td>
        <td>2000.00</td>
        <td>2500.00</td>
    </tr>
    <tr>
        <td>Pickup</td>
        <td>pickup</td>
        <td>pickup employee</td>
        <td>2000.00</td>
        <td>2500.00</td>
    </tr>
</table>

## vacations

VacationSeeder.php

<table>
    <tr>
        <th>Employee_id</th>
        <th>Vacation_type</th>
        <th>Start_date</th>
        <th>End_date</th>
        <th>Day_type</th>
        <th>Approve_status</th>
    </tr>
    <tr>
        <td>1</td>
        <td>holiday</td>
        <td>2025-03-25</td>
        <td>2025-03-25</td>
        <td>whole day</td>
        <td>approved</td>
    </tr>
    <tr>
        <td>1</td>
        <td>holiday</td>
        <td>2025-05-1</td>
        <td>2025-05-1</td>
        <td>whole day</td>
        <td>approved</td>
    </tr>
    <tr>
        <td>1</td>
        <td>holiday</td>
        <td>2025-05-6</td>
        <td>2025-05-6</td>
        <td>first half</td>
        <td>rejected</td>
    </tr>
    <tr>
        <td>1</td>
        <td>sick leave</td>
        <td>2025-05-15</td>
        <td>2025-05-15</td>
        <td>full day</td>
        <td>approved</td>
    </tr>
    <tr>
        <td>2</td>
        <td>holiday</td>
        <td>2025-05-12</td>
        <td>2025-05-12</td>
        <td>second half</td>
        <td>rejected</td>
    </tr>
    <tr>
        <td>3</td>
        <td>sick leave</td>
        <td>2025-05-18</td>
        <td>2025-05-18</td>
        <td>full day</td>
        <td>approved</td>
    </tr>
</table>

## messagetemplates

MessageTemplatesSeeder.php

<table>
    <tr>
        <th>Key</th>
        <th>Message</th>
    </tr>
    <tr>
        <td>holiday_approved</td>
        <td>your holiday was approved</td>
    </tr>
    <tr>
        <td>holiday_rejected</td>
        <td>your holiday was rejected</td>
    </tr>
    <tr>
        <td>announcement</td>
        <td>a new announcement has been posted check it out</td>
    </tr>
</table>




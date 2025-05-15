# Scenario 1 of Blue Sky Unlimited Final Demo

## Required for the demo
1. A big white screen (beamer)
2. A Tv screen or something similar
3. Cardboard box with generated QR code
4. All SS stuff - Database server - Mail server
5. Fresh mind

## Demonstration procedure
To begin with, we're going to try to stage the demonstration as accurately as possible, so as to make it as realistic as possible.

We would have several actors in the demonstration, including a customer, a worker at the post office where the parcel will be goign through, a HR manager who will manages our employees and finally a recipient of the parcel.

## Let's move on to the demonstration
1. Fenno and Jordi will discuss it  
    1.1   
    1.2  
    1.3
2. The customer will first create an account.
3. The customer will login and submit the send-package form which will generate a QR code to paste on it and also a link for Track and Trace.
4. The customer brings that parcel to a pickup point
5. The pickup point worker scans the package in.
6. The package will be transfered to all the generated hops our router has generated. (tv screen with Track and Trace)  
--> Simulation of scan in and scan out at each hop with every coworker.
7. The dispatcher in a distribution center will assign multiple packages to a loadbay which will be assigned to an available courier.
8. The dispatcher assigns a loadbay with packages to a courier so that he knwos at which loadbay he has to load the packages, the route for that courier will be generated once the loadbay is assigned to him.
9. Once in the one to last hop, which is a distribution center we will act again real.
--> scan out package from distribution center
--> scan in package as courier and puts the parcel in the van
--> Courier rides to the 'ADDRESS' location and delivers the package with oour without signature.
--> we will also have a receiver who will come get his package from a pickup point.

## All extreme cases where system or internal server errors can occur
1. SQL injection via input fields
Inputs such as `' OR 1=1 --` in a login or search field can cause a database leak if secure queries are not used.

2. XSS (Cross-Site Scripting) via text fields
Entering `<script>alert('Hacked!');</script>` in an address or name field, for example, can execute JavaScript on other pages.

3. CSRF attack via form buttons
A user can open a malicious link that submits a form in your app without permission if there is no CSRF protection.

4. Brute-force login attacks
A user can endlessly try passwords if there is no rate-limiting on the login page.

5. Placing many orders to overload the database
A user can create thousands of fictitious orders or packages, making the database slow or crashing.

6. Upload large files (HR profile pics)
If no file limit is set, a user can upload extremely large files to fill up the server space.

7. Injecting HTML or CSS into text fields
A user can enter `<style>body {display: none;}</style>`, causing the entire website to disappear.

8. Spamming API calls
If the customer create page (for example) is allowed to send unlimited requests without throttling, a user can overload the server by continuously refreshing the status.

9. Using back button after login/logout
If sessions are not cleared properly, a user can stay logged in after a logout by using the back button.
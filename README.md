# ServiceSpark

ServiceSpark is a powerful tool to help volunteer coordinators and volunteers keep tabs on hours, be notified of volunteer opportunities.  ServiceSpark code is presented as-is with no warranty of any kind.

ServiceSpark is maintained by @bradkovach for the United Way of Albany County.  To inquire about a hosted installation of ServiceSpark, please contact servicespark@unitedwayalbanycounty.org

## Install
1. Install CakePHP 2.x on your webserver. 
2. Make sure CakePHP is able to connect to your database.  
3. Copy the `Config/` directory somewhere safe.
4. Delete the `app` directory
5. Replace the contents of the `app/` directory with this repository.

### Database installation
Execute the contents of `sql/install.sql` on your database.

### Application Settings
Open `Config/bootstrap.php` and add the following at line ~28
```php
Configure::write('Solution.name','ServiceSpark');
Configure::write('Solution.description','makes your community better by helping you volunteer doing things you love.');
Configure::write('Google.maps.api_key', '---your---google---maps---api---key---here');
```

### Adjust Routing Prefixes
Open `Config/core.php` and add the following at line ~145
```php
Configure::write('Routing.prefixes', array('go', 'admin', 'coordinator', 'volunteer', 'supervisor', 'json') );
```

### Adjust the timezone for DB connection and PHP installation if necessary.

Type a comma `,` after line 9, a return and add the following line to adjust database time zone
```php
'settings' => array( 'time_zone' => "'-06:00'" )
```
### Start using ServiceSpark
Visit your ServiceSpark installation and create an account.  Before you can begin using ServiceSpark, you will also need to create an Organization.  At the moment, manually editing the `users` table is the only way to give yourself full administrative privileges.

### If you are attempting to create events
1. Create an organization
2. Join the organization
3. Find the permission row in `permissions` that corresponds to your organization id and your user id and set the `write` field to `1`
4. Logout and log back in to ServiceSpark.  You will now be able to coordinate events for your organization.

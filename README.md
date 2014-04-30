# ServiceSpark

ServiceSpark is a powerful tool to help volunteer coordinators and volunteers keep tabs on hours, be notified of volunteer opportunities.  ServiceSpark code is presented as-is with no warranty of any kind.

ServiceSpark is maintained by @bradkovach for the United Way of Albany County.  To inquire about a hosted installation of ServiceSpark, please contact servicespark@unitedwayalbanycounty.org

## Install
Install CakePHP 2.4 on your webserver.  Make sure CakePHP is able to connect to your database.  Replace the contents of the `app/` directory with this repository.

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

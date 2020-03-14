# ServiceSpark

ServiceSpark is a powerful tool to help volunteer coordinators and volunteers keep tabs on hours, be notified of volunteer opportunities.  ServiceSpark code is presented as-is with no warranty of any kind.

ServiceSpark is maintained by @bradkovach for the United Way of Albany County.  To inquire about a hosted installation of ServiceSpark, please contact servicespark@unitedwayalbanycounty.org

## Quick Install
Use the 5-minute installer from https://github.com/uwacwy/servicespark-installer

## Install

### 1. Clone Repository

```bash
git clone https://github.com/uwacwy/servicespark.git servicespark
```

#### Verify the installation...
```bash
tree -L 1 --dirsfirst -F servicespark
```

You should see the following output...

```
servicespark/
├── app/
├── cache/
├── install/
├── logs/
├── www/
├── composer.json
├── composer.lock
└── README.md

5 directories, 3 files
```

### 2. Install Dependencies with Composer

ServiceSpark uses plugin packages that are not in the Packagist.org repository.  As a result, enumerating the packages may take a very long time.  It may time out, so try again, as composer will pickup where it left off.

#### With `composer` installed globally...
```bash
cd servicespark
composer install
```

#### Install and use local `composer.phar` locally...
```bash
cd servicespark
curl https://getcomposer.org/installer | php
php composer.phar install
```

#### If Composer times out during `install`...
You may need to disable the composer process timeout, as the cakephp/cakephp repository is large.  Prepend your composer command with `COMPOSER_PROCESS_TIMEOUT=0`

```bash
# when installing globally...
COMPOSER_PROCESS_TIMEOUT=0 composer install
# when installing locally...
COMPOSER_PROCESS_TIMEOUT=0 php composer.phar install
```

### Database installation
Execute the contents of `sql/install.sql` on your database.

### Application Settings
Open and edit the following files and save removing `.default` from the filenames...

- `$/app/Config/servicespark.bootstrap.php.default`
- `$/app/Config/servicespark.events.php.default`

### Adjust Routing Prefixes
Open `Config/core.php` and add the following at line ~145
```php
Configure::write('Routing.prefixes', array('go', 'admin', 'coordinator', 'volunteer', 'supervisor', 'json') );
```

### Start using ServiceSpark
Visit your ServiceSpark installation and create an account.  Before you can begin using ServiceSpark, you will also need to create an Organization.

To make yourself a super administrator, edit your user row in the `users` table.

At the moment, manually editing the `users` table is the only way to give yourself full administrative privileges.

### To create events
1. Create an organization (Volunteer > Create Organization)
2. Join the organization (Volunteer > Join Organizations)
3. Find the permission row in `permissions` that corresponds to your organization id and your user id and set the `write` field to `1`
4. Logout and log back in to ServiceSpark.  You will now be able to coordinate events for your organization.

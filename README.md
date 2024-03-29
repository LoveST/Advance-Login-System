# Overview
This script is entended to help developers implement a friendly user login & management system.

Having all that, makes it easier to manage all users (with diffrent levels) & admins. 

# Features
- Login with Username & password
- Register new users
- Disable & Enable registeration
- Disable & Enable automatic user account activation
- Ban Users
- Reset password
- Custom Groups
- Custom Group Permissions
- Disable & Enable the script
- Disable & Enable logins for users
- Simple Integrated XP System
- Monitorize all users and activities
- Friendly **API**
- Custom Languages
- Custom Templates
- Custom Variables
- Custom API Functions
- Custom Database Connections
- Support for MySQLi & PDO connections ( more to come )

#### And many more features to come

# Requirements
- PHP Version : ``` 5.4 or greater ```

# Permissions
Make sure the ``` cache ``` file inside the desired template has a ``` CHMOD 777 ```

# To-Begin
Include the Core.php file to your desired script files.

```php
require "Core.php";
```

Initiate the class.

```php
$core = new \ALS\Core();
$core->initClasses();
```

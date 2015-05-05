PHP-Ziptastic
=============

A simple PHP class used to access the Ziptastic API.

Sample usage:

```php
$zt = new PHPZiptastic\Ziptastic(49504);

print $zt->city; // Grand Rapids

print $zt->lookup(34231)->city; // Sarasota

print $zt->lookup(44870); // Bloomingville, OH

print json_encode($zt); // {"city":"Bloomingville","state":"OH","zip":"44870"}
```

Errors and Exceptions
=============

By default, errors (zip not found, malformed zip passed) will place a message in the $error property, and return a self-reference. This looks like:

```php
print $zt->lookup(12344)->city; // null
print $zt->error; // Invalid zip code
```

If you would like to catch an exception instead, pass TRUE as a second constructor parameter:

```php
$zt = new Ziptastic(null, true);
$zt->lookup(12344); // exception thrown here
```

OR

```php
$zt = new Ziptastic(12344, true); // exception thrown here
```

For all errors, the exception throw is the base PHP Exception class.

Installation
=============

Include Ziptastic.php or install the composer package.

Ziptastic
=============
Ziptastic is a simple API that allows people to ask which Country,State and City are associated with a Zip Code.

More info at http://daspecster.github.com/ziptastic/

Many thanks to ElevenBaseTwo (http://blog.elevenbasetwo.com/) for shortening our forms!

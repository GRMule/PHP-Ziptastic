PHP-Ziptastic
=============

A small PHP class used to access the Ziptastic API.

Sample usage:

$zt = new Ziptastic(49504);

print $zt->city; // Grand Rapids

print $zt->lookup(34231)->city; // Sarasota

print $zt->lookup(44870); // Bloomingville, OH

print json_encode($zt); // {"city":"Bloomingville","state":"OH","zip":"44870"}

Ziptastic
=============
Ziptastic is a simple API that allows people to ask which Country,State and City are associated with a Zip Code.

More info at http://daspecster.github.com/ziptastic/

Many thanks to ElevenBaseTwo (http://blog.elevenbasetwo.com/) for shortening our forms!

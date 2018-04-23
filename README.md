# Cacti Templates to query FreeRADIUS 3.0.x stats

## Introduction
Contains templates to query your FreeRADIUS server stats. Query authentication, accounting and proxy-authentication stats. Requires radclient.

## Installation
Copy freeradius.php into folder scripts in cacti. Install radclient and edit the path to radclient in freeradius.php. Import all templates in cacti.

## Configuring the FreeRADIUS server
Add your cacti server to FreeRADIUS nas list and make sure the shared secrets match.

https://wiki.freeradius.org/config/Status

## Debugging the Template

Run the freeradius.php locally using arguments. Change into cacti/scripts and issue
./freeradius auth 10.10.10.23 18121 mysecret

Your should get some output received from FreeRADIUS or an error message. Also consider the cacti logs.

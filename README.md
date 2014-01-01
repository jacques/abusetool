Abusetool
=========

Abusetool is the abuse desk tool I wished that Joyent had when I was dealing with
abuse there.  This is a work in progress to start gathering data from various
sources to provide data via API calls (a seperate restify app is in development)
so that CIDR ranges can be blocked using by various chef cookbooks (i.e. Spammers
and compromised hosts can be blocked from sending SPAM or connecting to apache).

TODO
----

 - [ ] - Track abuse by IP and link back to CIDR block
 - [ ] - Link CIDR's to the ASN announcing the netblock
 - [ ] - Provide a report showing IP's where abuse is originating from
 - [ ] - Write an importer to import ASN details
 - [ ] - Link to HE's BGP pages for more information
 - [ ] - Cache WHOIS lookups
 - [ ] - Parse incoming emails for WordPress dictionary attacks

Authors
-------

 * Jacques Marneweck (jacques@powertrip.co.za)

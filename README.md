ddns-update-for-ispconfig
=========================

This php script use ISPConfig 3 Remote API to update an ip in the DNS.

URL Call : http://server/?username=ispcruser&password=ruserpass&hostname=dns&myip=ip

Where :
  - ispcruser : Remote Username in ISPConfig
  - ruserpass : Remote User password in ISPConfig
  - hostname : DNS A entry to update
  - myip : IP
  
The remote ispconfig username must have 
  - DNS Zone function
  - DNS A function

You must update the URL of your ISPConfig Installation in $soap_location and $soap_uri

Sources and ref :
- https://www.howtoforge.de/forum/threads/dyndns-mit-ispconfig.8089/
- http://www.ispconfig.org



ddns-update-for-ispconfig
=========================

This php script use ISPConfig 3 Remote API to update an ip in the DNS.

URL Call : http://server/?username=ispcruser&password=ruserpass&hostname=dns&myip=ip

Where :
  - ispcruser : Remote Username in ISPConfig
  - ruserpass : Remote User password in ISPConfig
  - hostname : DNS A (ipv4) or AAAA (ipv6) entry to update
  - myip : IPv4 or IPv6
  
The remote ispconfig username must have 
  - DNS Zone function
  - DNS A function
  - DNS AAAA function

You must update the URL of your ISPConfig Installation in $soap_location and $soap_uri

Sources and ref :
- https://www.howtoforge.de/forum/threads/dyndns-mit-ispconfig.8089/
- http://www.ispconfig.org



FIX: Minor modification to fix type of dns entry that was not working

Example commands in shell:

curl -s "https://example.com/dyn/?username=ispcruser&password=ruserpass&hostname=dyn.example.com&myip=$(dig @resolver1.opendns.com ANY myip.opendns.com +short)"

curl -s "https://example.com/dyn/?username=ispcruser&password=ruserpass&hostname=dyn.example.com&myip=$(dig @resolver1.opendns.com A myip.opendns.com +short -4)"

curl -s "https://example.com/dyn/?username=ispcruser&password=ruserpass&hostname=dyn.example.com&myip=$(dig @resolver1.opendns.com AAAA myip.opendns.com +short -6)"


ddns-update-for-ispconfig
=========================

This php script uses ISPConfig 3 Remote API to update an ip in the DNS.

If files are placed in directory named dyn in your website:
URL Call : https://example.com/dyn/?username=ispcruser&password=ruserpass&hostname=dyn.example.com&myip=ip

Where :
  - ispcruser : Remote Username in ISPConfig
  - ruserpass : Remote User password in ISPConfig
  - hostname : DNS A (ipv4) or AAAA (ipv6) entry to update
  - myip : IPv4 or IPv6
  
The remote ispconfig username must have the following permissions: 
  - DNS Zone function
  - DNS A function
  - DNS AAAA function

You must update the URL of your ISPConfig Installation in $soap_location and $soap_uri

Sources and ref :
- https://www.howtoforge.de/forum/threads/dyndns-mit-ispconfig.8089/
- http://www.ispconfig.org

- Based on the work of https://github.com/DIXINFOR/ddns-update-for-ispconfig



FIX: Minor modification to fix type of dns entry that was not working

Example commands in shell:

curl -s "https://example.com/dyn/?username=ispcruser&password=ruserpass&hostname=dyn.example.com&myip=$(dig @resolver1.opendns.com ANY myip.opendns.com +short)"

curl -s "https://example.com/dyn/?username=ispcruser&password=ruserpass&hostname=dyn.example.com&myip=$(dig @resolver1.opendns.com A myip.opendns.com +short -4)"

curl -s "https://example.com/dyn/?username=ispcruser&password=ruserpass&hostname=dyn.example.com&myip=$(dig @resolver1.opendns.com AAAA myip.opendns.com +short -6)"


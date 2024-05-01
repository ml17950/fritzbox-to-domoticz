# fritzbox-to-domoticz

Ein kleines Projekt, um die Energie-Daten einer FRITZ!DECT 200 auszulesen und bei Domoticz einzutragen.

Einfach via cron auf einem Linux-System ausführen:

```
*/5 * * * *		/usr/bin/php /home/martin/fritzbox-to-domoticz/run-every-5-minutes.php
```
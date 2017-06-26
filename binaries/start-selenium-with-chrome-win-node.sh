#!/usr/bin/env bash
java -Xmx1024m -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.3.1.jar -role node -hub http://localhost:4444/grid/register -port 5555

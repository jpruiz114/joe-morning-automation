# Shell Scripts

In OSX, the following might be needed:

chmod +x chromedriver

chmod +x start-selenium-with-chrome-osx.sh

## About Selenium

Yes can run server on different ports and the server can handle requests from sever clients at the same port. And are you trying to set up hub/node setup here ?

### HUB

java -Xmx1024m -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.3.1.jar -role hub -port 4444

### Node

java -Xmx1024m -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.3.1.jar -role node -hub http://localhost:4444/grid/register -port 5555

java -Xmx1024m -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.3.1.jar -role node -hub http://localhost:4444/grid/register -port 6666

java -Xmx1024m -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.3.1.jar -role node -hub http://localhost:4444/grid/register -port 7777

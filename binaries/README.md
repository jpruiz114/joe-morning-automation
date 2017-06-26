# Shell Scripts

In OSX, the following might be needed:

chmod +x chromedriver

chmod +x start-selenium-with-chrome-osx.sh

## About Selenium

Yes can run server on different ports and the server can handle requests from sever clients at the same port. And are you trying to set up hub/node setup here ?

### HUB

java -jar selenium-server-standalone-2.44.0.jar -role hub -port 4444

### Node

java -jar selenium-server-standalone-2.44.0.jar -role node -hub http://localhost:4444/grid/register -port 5557

# Joe Morning Automation

More details to be added soon

![Alt text](/assets/cat.gif?raw=true "Details to be added soon")

To install java, refer to this URL - http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html

## Configuration file

Copy .env.SAMPLE to a new file called .env

## BrowserStack info

https://www.browserstack.com/automate/timeouts

### Timeouts

Timeouts occur when one or more of the elements during testing become inactive for a specified period of time.
The following timeouts may appear in the STOP_SESSION logs:

### SO_TIMEOUT

The hub waits for a maximum of 240 seconds before an unresponsive browser is killed, changing the session status to ERROR on the dashboard.

### IDLE TIMEOUT

If a session is idle for more than 90 seconds, the session is stopped, changing the session status to TIMEOUT on the dashboard.

### SESSION LIMIT REACHED

If a session is running for more than 7200 seconds (2 hours), the session is stopped, changing the session status to TIMEOUT on the dashboard.

ws-webcam
=========

A HTML5-based webcam you can control using a web browser that supports *websockets*
and *getUserMedia* streams.

### Web Server Requirements

* Node.js
* PHP 5.x
* PHP GD support
* Directory that hosts the files located in the folder */webcam* should be writeable
+rw by the web server

### Web Browser Requirements

* Webcam **recorder** and **viewer** requires [browser support for
*WebSockets*](http://caniuse.com/websockets)
* Webcam **recorder** requires [browser support for *getUserMedia*
stream](http://caniuse.com/stream)

### Network Requirements

* If you need to view the webcam outside of your local network, you have two
options:
 1. On your router's firewall, open a port (eg. 80) for the web server hosting
 the PHP files. Open another port (eg. 5000) for the Node.js WebSockets server.
 2. Put the files located inside the */webcam* folder on an external web host,
 such as DreamHost or HostGator. For hosting the Node.js WebSocket server, I
 would go with Heroku. You can follow the instructions on
 [Getting started with Node.js on
 Heroku](https://devcenter.heroku.com/articles/getting-started-with-nodejs)

### Running the WebSockets Server on local network

Go to the directory:

    $ cd websocketserver

Install all the dependencies first:

    $ npm install

Verify that the /node_modules has been created:

    $ ls
    Procfile	README.md	index.html	node_modules	package.json	server.js
    $ ls node_modules
    express	ws

Then, run the server:

    $ node server.js
    http server listening on 5000
    websocket server created

Verify that it is running by going to localhost:5000 on your web browser.

### Running the WebSockets Server on Heroku

Follow the procedure for the local network install of the WebSockets Server.
Assuming you have created an account on Heroku, and installed Heroku Toolbelt,
you can follow these steps to deploy our WebSocket Server.

    $ heroku login

Store the websocketserver in Git:

    $ git init
    $ git add .
    $ git commit -m "init"

Deploy the application to Heroku:

    $ heroku create
    $ heroku labs:enable websockets
    $ git push heroku master

Make note of the application URL that was generated for you.
(eg. sugarpop-tar-6543.herokuapp.com) You will need to enter that URL into the
settings of our app.

### Changing App Settings

There are a couple of important settings you might want to change before using
this app.

1. The Admin password is defaulted to: 1qazxsw2
  * Edit the **auth.inc** file located in the /webcam folder, and change the password there.
2. The WebSocket Server is defaulted to: localhost:5000
  * Edit the **config.json** file, and replace "locahost:5000" with the
URL of the production WebSocket Server.
  * You can also go to the URL of the webcam and go to the Admin section to
  change this setting.

### Start Webcam

Place the device you wish to use as the **recorder** in a location that you
want to monitor.

**NOTE:** If you wish to monitor more than one location, then you would
need to duplicate the webcam folder into another web folder location. You could
setup a web folder called */home* and another called */work*. You would need to
copy the contents of */webcam* into each of those folders. Then, you would have
to deploy another Node.js WebSocket server for the second location.

Device/browser/OS configurations I tested and support are:
* Chrome on Windows, Mac, and Android
* FireFox on Windows, Mac, and Android


1. Start the webcam app on the recorder device by going to the webcam URL.
eg. myserver.com/webcam/ and then selecting "Start Webcam" from the Menu.
2. If your device supports the getUserMedia streams API, you will be asked
permission to access the camera. When you choose "yes", the camera will
automatically start streaming.

### View webcam

The webcam does not stream video, but takes snapshots based on motion detection
or by manually requesting a shot. You can request a snapshot by tapping/clicking on
the image. The option is available to turn motion detection ON / OFF.

Device/browser/OS configurations I tested and support are:
* Chrome on Windows, Mac, and Android
* FireFox on Windows, Mac, and Android
* Safari on Mac, iOS 6 and higher
* IE 10 and higher

### Snapshots

You can review all the snapshots that have ever been taken, by selecting
"Snapshots" from the menu. If you've logged in as an Admin, you can:

* Selectively remove snapshots that are no longer needed.
* Create animated GIF from snapshots taken. These are saved in the Recordings.

### Recordings

You can review all the recordings (animated GIFs) that have been saved, by
selecting "Recordings" from the menu. If you've logged in as Admin, you can:

* Selectively remove recordings that are no longer needed.

### Admin

Once you login successfully, you have the added capability to:

* delete snapshots / recordings
* create recordings (Animated GIFs) from the snapshots taken
* change configuration settings

### Adding the web app to the home screen of your smartphone

As a bonus, this web app has been written to work as a fullscreen web app
that can be launched from your smartphone's home screen. For now, I only
support Android and iOS.

### Future Work

* It would be nice to support multiple locations from a single web folder
installation.
* WebRTC might be something that enables live video streaming. However, not
many browsers support it.
* Lazy load images in the Snapshots and Recordings area.

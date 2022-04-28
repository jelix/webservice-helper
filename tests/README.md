Unit tests
===========


To run the example and launch unit tests, you need to install Docker.


build
------

Then, before launching containers, you have to run this command:

```
./run-docker build
```

launch
-------

To launch containers, just run `./run-docker`.

You can then load in your browser:

```
http://localhost:8316/service.php?class=contactManager
```

To run tests, use the command:

```
./app-ctl unit-tests
```

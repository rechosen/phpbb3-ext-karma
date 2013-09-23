phpBB Extension - Karma System
==============================

This is a karma extension for phpBB 3.1 and up. Development was started as part of the Google Summer of Code 2013. The extension allows registered board users to add or subtracts points from other users' karma scores by rating their posts. The karma scores are displayed publicly in places where user info is displayed (next to posts, on their profiles etcetera). Karma that is deemed unfair may be reported to the board moderators, who may then edit or delete the reported karma.


Installation
------------

To install this extension, download a [zip archive](https://github.com/rechosen/phpbb3-ext-karma/archive/master.zip) of it, create the folder ext/phpbb/karma in your phpBB root directory and extract the zip contents there. Then go to your Administration Control Panel, go to CUSTOMISE, then Extensions and enable the extension there.


Known Issues
------------

Though lots of work has gone into the extension during the Google Summer of Code, it is not yet entirely finished. The following known issues may affect the use and/or further development of the extension:

* **Only the prosilver style is supported at the moment.**
* **It is not yet possible to view all given karma in the ACP.** Reported karma may be viewed and moderated in the MCP, though.
* **The confirm box for deleting karma as a moderator doesn't work at the moment because of [issue 11850](http://tracker.phpbb.com/browse/PHPBB3-11850).** It should start working again once this bug in phpBB is fixed.
* No spam protection is implemented yet.
* The test suite is far from complete.
* Permissions are quite coarse-grained at the moment (only global permissions are implemented).
* Only a British English translation is included, but it's easy to add more languages.
* Code aesthetics may not be pleasing to every eye.

Also see the TODO comments in the code and the [design document](https://docs.google.com/document/d/1bTk5EDqtDMWwS0uMfG-93AMS8PQJJORvCsiyrelgPdg/edit?usp=sharing).


Extending the karma system
--------------------------

To allow users to give karma on other entities than posts, follow these steps:

1. Write a class implementing phpbb_ext_phpbb_karma_includes_type_interface, and add it to services.yml with a name tag of 'karma.type'. Pro tip: base your class on phpbb_ext_phpbb_karma_includes_type_base to easily get a lot of useful dependencies injected for you.
2. Add links that allow users to give karma on the new entity. Use the 'givekarma/{karma_type_name}/{item_id}' controller, and the 'score' GET parameter with 'positive' or 'negative' to prefill the score radio buttons. See event/main_listener.php and styles/prosilver/template/event/* of this extension for some example material.
3. If you like, add the user karma score wherever you display user data. Again, see event/main_listener.php and styles/prosilver/template/event/* of this extension for some example material.
4. Whenever an item of your newly added karma_type is deleted, make sure delete_karma_given_on_item() of karma.includes.manager is called.
5. You're done!


Tests and Continuous Integration
--------------------------------

[![Build Status](https://travis-ci.org/rechosen/phpbb3-ext-karma.png?branch=master)](https://travis-ci.org/rechosen/phpbb3-ext-karma)

We use Travis-CI as a continuous integration server and PHPUnit for our unit testing. See more information on the phpBB development wiki.


License
-------
[GPLv2](license.txt)

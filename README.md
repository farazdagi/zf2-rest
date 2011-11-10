ZF2 RESTful Service
=======================

Introduction
------------
This is simple demonstration of implementing RESTful web-service using ZF2.

Installation
------------

    git clone --recursive git@github.com:farazdagi/zf2-rest.git

Alternatively:

    git clone git@github.com:farazdagi/zf2-rest.git
    git submodule update --init --recursive

In order to run tests you need to create database:

    INSERT INTO users VALUES (1, 'horus'), (3, 'fulgrim');

CHANGELOG
------------
Version 0.3:

- Functional and Unit Tests Added
- Implemented all sample gist methods (GET, PUT, POST, PATCH, DELETE)


Version 0.2:

- Updated ZF2 Lib (2238faa)
- Created custom Http\PhpEnvironment\Request (to handle custom HTTP types)
- Controller skeletons created
- User and Gist models defined

Version 0.1:

- Integrated [ZendSkeletonApplication](https://github.com/zendframework/ZendSkeletonApplication)

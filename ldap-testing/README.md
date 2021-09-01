# LDAP testing

![Docker Pulls](https://img.shields.io/docker/pulls/pydio/ldap-testing.svg)
![Docker Stars](https://img.shields.io/docker/stars/pydio/ldap-testing.svg)
![](https://images.microbadger.com/badges/image/pydio/ldap-testing.svg)

This project provides an easy way to generate various simple _dummy_ docker images that we commonly use to test Pydio Cells against an LDAP external directory.

The generated images are then uploaded to [the Docker hub](https://hub.docker.com/r/pydio/ldap-testing/).

To launch the image simply run:

```sh
docker run --rm -p "389:389" pydio/ldap-testing:tiny --loglevel debug
```

## How To Use

### Pre-requisite

In order to successfully run the makefile, you need to:

- have make and php installed on your local workstation
- clone this repository

### Build

To build, simply run following commands:

```sh
# To generate the default minimal image with only ~10 users
make build
# To generate a larger image
export TAG=medium && make build
```

### Publish

***Pydio Mainteners only***:

You should have a docker account configured on your machine.
Once you have modified and tested the image you want to update, only run:

```sh
make tiny # ~10 users
make medium #  ~12k users
```

This:

- generates dummy ldif files with users and groups
- generates and publishes the docker image

## How To Build a Custom Image

You might want to customise your image and publish it in another docker hub account.

To do so, you might impact following files:

- the `Makefile` to define main variables
- the `dummy-users.csv` (that can be found under `assets/tiny` or `assets/medium` to change the user that are used.

Please make extra care not to modify the main `pydio/ldap-testing` blindly: we rely on some of the well known values for our integration tests.

## Built upon

We used the [Mockaroo](https://mockaroo.com/) to generate our set of test data.

The image we use is based on the nice [osixia/openldap:latest](https://github.com/osixia/docker-openldap) docker image that provides a running OpenLDAP in a docker container out of the box.

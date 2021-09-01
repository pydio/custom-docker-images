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

You can then simply bind to the LDAP with admin user: `cn=admin,dc=example,dc=com / admin`.

All other users password is `P@ssw0rd` by default.

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

## Configure connector in Pydio Cells

If you are running a Pydio Cells _Enterprise_ or _Connect_ distribution, you can then simply add a connector to import the users from the newly created LDAP.

If you have left everything to its default:

Go to: `Admin Console >> Identity management >> Authentication >> AD / LDAP >> +Directory`

In **General Options** tab:

- Choose a human friendly label
- Define a synchronisation rate: Cells internal repository is an automated copy of a sub part of your LDAP. Thus at next sync, any modification made in your _main_ repo will be propagated to Cells and any change directly made via Cells will be overwritten.

In **Server Connection** tab:

- Host: Define your host (usually `localhost` if you have launched a simple container or `<the name of your service>` in docker compose)
- Connection Type: Not secure is OK for a quick test
- Binding DN: dn of a power user, in our case, `cn=admin,dc=example,dc=com`
- Binding Password: by default, `admin`

In **Users Filter** tab:

- DN: `dc=example,dc=com`
- Filter: `(objectClass=inetOrgPerson)`
- ID Attribute Name: `uid`

In **Simple Mapping** tab, you can link attribute values that are defined in the LDAP to user properties in Cells, typically:

- `displayName` attribute (Left) to `Display Name` (Right) prop in Cells
- `mail` to `Email`

In **MemberOf** tab, you can link memberOf attributes of the LDAP to **Roles** in Cells. You can then define permissions based on these automatically created roles on Cells side. Typically to give access to a given workspace only if a LDAP user is member of `cn=VIPs,ou=traversal,ou=groups,dc=example,dc=com` group in your LDAP.

- Turn on the `Enable MemberOf Mapping` toggle
- Mapping:
  - Left Attribute: `memberOf`
  - Rule String: ` ` (keep empty)
  - Right Attribute: `Roles` (keep default)
- Groups Filtering:
  - DN: `ou=groups,dc=example,dc=com`
  - Filter: `(objectClass=groupOfNames)`
- Leave everything else unchanged

Just save and you can then manually trigger a first resync of the user repository by going to:  
`Admin Console >> Cells Flow >> (Your ConnectionLabel) > Synchronize external directories  >> RUN NOW`

## How To Build a Custom Image

You might want to customise your image and publish it in another docker hub account.

To do so, you might impact following files:

- the `Makefile` to define main variables
- the `dummy-users.csv` (that can be found under `assets/tiny` or `assets/medium` to change the user that are used.

Please make extra care not to modify the main `pydio/ldap-testing` blindly: we rely on some of the well known values for our integration tests.

## Built upon

We used the [Mockaroo](https://mockaroo.com/) to generate our set of test data.

The image we use is based on the nice [osixia/openldap:latest](https://github.com/osixia/docker-openldap) docker image that provides a running OpenLDAP in a docker container out of the box.

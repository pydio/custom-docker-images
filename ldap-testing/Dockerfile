FROM osixia/openldap:latest
LABEL authors="Pydio Team <contact@pydio.com>"

ADD build/environment /container/environment/01-custom
ADD build/certs /container/service/slapd/assets/certs
ADD build/bootstrap /container/service/slapd/assets/config/bootstrap

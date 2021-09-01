# Default variables
OWNER=pydio
IMG_NAME=ldap-testing
NAME=$(OWNER)/$(IMG_NAME)
CURR_DIR=$(shell pwd)
# we build a tiny image by default
TAG ?= tiny
# this is the base DN for the generated user and group repository
BASE_DN="dc=example,dc=com"


# simply insures targets are never cached: "a phony target is simply a target that is always out-of-date"...
.PHONY: debug clean prepare build-nocache publish tiny medium

debug:
	@echo 'Tag: $(TAG)'

clean:
	@rm -rf ./build/bootstrap

prepare: clean
	@mkdir -p ./build/bootstrap
	@cp -R ./assets/$(TAG)/ldif ./build/bootstrap 
	@php ./scripts/generate-users-ldif.php $(CURR_DIR)/assets/$(TAG)/dummy-users.csv $(BASE_DN) >> ./build/bootstrap/ldif/70-users.ldif
	@cp -R ./assets/schema ./build/bootstrap 
	@cp -R ./assets/environment ./build
	@cp -R ./assets/certs ./build 

build: prepare
	docker build -t $(NAME):$(TAG) --rm .

build-nocache: prepare
	docker build -t $(NAME):$(TAG) --no-cache --rm .

publish: build ## push to Docker 
	@docker login
	docker push $(NAME):$(TAG)
	@echo 'published $(NAME):$(TAG)'

tiny: TAG=tiny
tiny: publish

medium: TAG=medium
medium: publish

#!/bin/bash

# get version from composer.json
LIB_VERSION="$(cat composer.json | tr { '\n' | tr , '\n' | tr } '\n' | grep "version" | awk  -F'"' '{print $4}')"

# update docs
composer build:docs
DOCS_STATUS="$(git status)"
echo $DOCS_STATUS

git tag ${LIB_VERSION}
git push origin ${LIB_VERSION}
#!/bin/bash

# get version from composer.json
LIB_VERSION="$(cat composer.json | tr { '\n' | tr , '\n' | tr } '\n' | grep "version" | awk  -F'"' '{print $4}')";

# update docs
echo "Building api docs"...;
php phpDocumentor.phar --defaultpackagename=Sdfcloud --ignore=**/*.TestSuite.php run -d src -t docs/api;

# commit docs if change is present
GIT_STATUS="$(git status)";
if [[ "$GIT_STATUS" == *"docs/api"* ]]; then
  COMMIT_MSG="Updating docs for build $LIB_VERSION";
  echo $COMMIT_MSG;
  git add .
  git commit -m "$(echo $COMMIT_MSG)"
  git push origin master
fi

# push build tag
git tag ${LIB_VERSION}
git push origin ${LIB_VERSION}
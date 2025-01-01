#!/bin/bash

DIRECTORY=$(dirname $0)

for package in build-magento generate-matrix
do
    rm -rf $DIRECTORY/$package/dist
    npm --prefix $DIRECTORY/$package install
    npm --prefix $DIRECTORY/$package run format:write
    npm --prefix $DIRECTORY/$package run package
done


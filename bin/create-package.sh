#! /bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )"/.. && pwd )"
PLUGINSLUG="$(basename $DIR)"
TEMPPATH="/tmp/$PLUGINSLUG"
ZIP_PATH="$DIR/../$PLUGINSLUG.zip"
EXCLUDES_FILE="$DIR/bin/excludes.txt"
rsync -r --delete --exclude-from=$EXCLUDES_FILE $DIR/ $TEMPPATH/
cd $TEMPPATH
zip -rq $ZIP_PATH ./
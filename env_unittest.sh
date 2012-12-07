#!/bin/bash


if [ $1 = '-d' ] ; then
  export PM_UNIT_DB_USER=root
  export PM_UNIT_DB_NAME=wf_ralph
  export PM_UNIT_DB_PASS=atopml2005
  export PM_UNIT_DB_HOST=localhost
  export HTTP_REFERER="http://192.168.11.30:8080"
  echo "env unit test set."
  bash -i
else
  if [ $1 = '-u' ] ; then
    unset PM_UNIT_DB_USER
    unset PM_UNIT_DB_NAME
    unset PM_UNIT_DB_PASS
    unset PM_UNIT_DB_HOST
    unset HTTP_REFERER
    echo "env unit test unset."
    bash -i
  else
    echo "Incorrect parameter, please use -d or -u\n";
  fi
fi

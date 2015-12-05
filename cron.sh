#!/bin/sh

myip=`curl -s checkip.dyndns.org | sed -e 's/.*Current IP Address: //' -e 's/<.*$//'`
echo "$(myip)"

if  dig www.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is production"
if ps -ef | grep -v grep | grep \/\/studysauce ; then
    exit 0
else
    wget --no-check-certificate -O /dev/null -o /dev/null https://studysauce.com/cron &
    exit 0
fi
fi


if  dig test.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is test"
if ps -ef | grep -v grep | grep test\.studysauce ; then
        exit 0
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron &
        exit 0
fi
fi


if  dig staging.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is staging"
if ps -ef | grep -v grep | grep staging\.studysauce ; then
        exit 0
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://staging.studysauce.com/cron &
        exit 0
fi
fi


if  dig cerebro.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is cerebro"
if ps -ef | grep -v grep | grep cerebro\.studysauce ; then
        exit 0
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://cerebro.studysauce.com/cron &
        exit 0
fi
fi

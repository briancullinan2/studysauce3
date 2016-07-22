#!/bin/sh

myip=`curl -s checkip.dyndns.org | sed -e 's/.*Current IP Address: //' -e 's/<.*$//'`
echo "$(myip)"

if  dig www.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is production"
if ps -ef | grep -v grep | grep \/\/studysauce ; then
    goto end;
else
    wget --no-check-certificate -O /dev/null -o /dev/null https://studysauce.com/cron &
    goto end;
fi
fi


if  dig test.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is test"
if ps -ef | grep -v grep | grep test\.studysauce ; then
        goto testCron;
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron &
        goto testCron;
fi

# check to see if cron validation is running, which is always should be
testCron:
if ps -ef | grep -v grep | grep test\.studysauce | grep cron\/validate ; then
        goto end;
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron/validate &
        goto end;
fi
fi


if  dig staging.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is staging"
if ps -ef | grep -v grep | grep staging\.studysauce ; then
        goto end;
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://staging.studysauce.com/cron &
        goto end;
fi
fi


if  dig cerebro.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is cerebro"
if ps -ef | grep -v grep | grep cerebro\.studysauce ; then
        goto end;
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://cerebro.studysauce.com/cron &
        goto end;
fi
fi

end:
exit 0
#!/bin/sh

myip=`curl -s checkip.dyndns.org | sed -e 's/.*Current IP Address: //' -e 's/<.*$//'`
echo "$(myip)"

if  dig www.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is production"
if ps -ef | grep -v grep | grep \/\/studysauce ; then
    goto end
else
    wget --no-check-certificate -O /dev/null -o /dev/null https://studysauce.com/cron &
    goto end
fi
fi


if  dig test.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is test"

# check to see if cron validation is running, which is always should be
if ps -ef | grep -v grep | grep cron\/validate ; then
        goto test
else
        cd /var/www/studysauce3/
        if ! git pull | grep "Already up-to-date" ; then
            ./update_test.sh
        fi
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron/validate &
        goto test
fi

test:
if ps -ef | grep -v grep | grep test\.studysauce ; then
        goto end
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron &
        goto end
fi
fi


if  dig staging.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is staging"
if ps -ef | grep -v grep | grep staging\.studysauce ; then
        goto end
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://staging.studysauce.com/cron &
        goto end
fi
fi


if  dig cerebro.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
echo "This is cerebro"
if ps -ef | grep -v grep | grep cerebro\.studysauce ; then
        goto end
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://cerebro.studysauce.com/cron &
        goto end
fi
fi

end:
exit 0
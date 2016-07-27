#!/bin/sh

myip=`curl -s checkip.dyndns.org | sed -e 's/.*Current IP Address: //' -e 's/<.*$//'`
echo "$(myip)"

if  dig www.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
    echo "This is production"
    if ps -ef | grep -v grep | grep \/\/studysauce ; then
        echo "Cron already running."
    else
        wget --no-check-certificate -O /dev/null -o /dev/null https://studysauce.com/cron &
    fi
fi


if  dig test.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
    echo "This is test."
    cd /var/www/studysauce3/
    if ! git pull | grep "Already up-to-date" ; then
        echo "Updating..."
        ./update_test.sh
    fi

    export DISPLAY=:10
    export PATH=$PATH:/home/public/firefox
    cd /home/public/
    if ps -ef | grep -v grep | grep displaybuffer ; then
        echo "Display already running."
    else
        echo "Starting display server."
        screen -dmS displaybuffer xvfb-run java -jar selenium-server-standalone-2.53.1.jar -port 4444 &
    fi

    # check to see if cron validation is running, which is always should be
    if ps -ef | grep -v grep | grep cron\/validate ; then
        echo "Cron already running."
    else
        echo "Starting validation."
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron/validate &
    fi

    if ps -ef | grep -v grep | grep test\.studysauce\.com\/cron ; then
        echo "Cron already running."
    else
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron &
    fi

fi


if  dig staging.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
    echo "This is staging"
    if ps -ef | grep -v grep | grep staging\.studysauce ; then
        echo "Cron already running."
    else
        wget --no-check-certificate -O /dev/null -o /dev/null https://staging.studysauce.com/cron &
    fi
fi


if  dig cerebro.studysauce.com | grep '^[^;].*IN\sA' | grep "$myip" ; then
    echo "This is cerebro"
    if ps -ef | grep -v grep | grep cerebro\.studysauce ; then
        echo "Cron already running."
    else
        wget --no-check-certificate -O /dev/null -o /dev/null https://cerebro.studysauce.com/cron &
    fi
fi


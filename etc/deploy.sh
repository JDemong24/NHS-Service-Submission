cd /home/ec2-user/pidaychallenge
git pull

cd /var/www/html
cp -r /home/ec2-user/pidaychallenge/web/* .
rm debug*.php

cd /var/www/vendor
cp -r /home/ec2-user/pidaychallenge/vendor/* .
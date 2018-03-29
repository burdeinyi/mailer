#!/usr/bin/env bash

line=$(head -n 1 /etc/hosts)
line2=$(echo $line | awk '{print $2}')
echo "$line $line2.localdomain $(hostname)" >> /etc/hosts

echo "$line $line2.localdomain $(hostname)"
cat /etc/hosts

hostname >> /etc/mail/relay-domains

m4 /etc/mail/sendmail.mc > /etc/mail/sendmail.cf

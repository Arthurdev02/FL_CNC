#!/usr/bin/sh
mariadb gamedb -uroot -pValeurDeVotreChoix < /root/init.sql
echo "Restauration terminée"
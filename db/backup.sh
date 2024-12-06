#!/usr/bin/sh
mariadb-dump gamedb -uroot -pValeurDeVotreChoix > /root/Backup-`(date -I)`.sql
echo "Sauvegarde terminée"
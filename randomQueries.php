<?php
/*

get vidlog with users/episodes:

SELECT FROM_UNIXTIME(  `vidlog`.`timestamp` ) ,  `vidlog`.`quality` ,  `users`.`username` ,  `episodes`.`name` 
FROM  `vidlog` 
JOIN  `users` ON  `users`.`uid` =  `vidlog`.`uid` 
JOIN  `episodes` ON  `episodes`.`eid` =  `vidid` 
ORDER BY  `vidlog`.`vidlogid` DESC 

suck up all the latest failed ips

select users.uid, users.username, iplog.ip from users left join iplog on iplog.uid = users.uid GROUP BY users.uid ORDER BY iplog.timestamp DESC 

*/
?>

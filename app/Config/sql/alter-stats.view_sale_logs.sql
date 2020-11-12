alter table stats add column eid varchar(128) after `transactionid`;
drop view view_sale_logs;
create view view_sale_logs as
select `companies`.`id` AS `comid`,`agents`.`id` AS `agid`,
	cast(`stats`.`trxtime` as date) AS `date`,`stats`.`eid` AS `email`,
	`companies`.`officename` AS `office`,`agents`.`ag1stname` AS `agent`,
	`sites`.`sitename` AS `site`,`types`.`typename` AS `link`,
	`fees`.`earning` AS `earning`,`fees`.`price` AS `payout`,
	`stats`.`transactionid` AS `clickid` 
from (((((`stats` join `sites`) join `types`) join `fees`) join `companies`) join `agents`) 
where ((`stats`.`sales_number` = 1) and (`stats`.`siteid` = `sites`.`id`) and (`stats`.`typeid` = `types`.`id`) 
	and (`types`.`id` = `fees`.`typeid`) and (`stats`.`companyid` = `companies`.`id`) 
	and (`stats`.`agentid` = `agents`.`id`))
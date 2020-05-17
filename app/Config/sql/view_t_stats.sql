create VIEW `view_t_stats` AS
select `t`.`trxtime` AS `trxtime`,`t`.`siteid` AS `siteid`,
`s`.`sitename` AS `sitename`,`t`.`agentid` AS `agentid`,
`a`.`username` AS `username`,`a`.`username4m` AS `username4m`,
`b`.`ag1stname` AS `ag1stname`,`b`.`aglastname` AS `aglastname`,
a.status,
`b`.`companyid` AS `companyid`,`c`.`officename` AS `officename`,
`t`.`raws` AS `raws`,`t`.`uniques` AS `uniques`,
`t`.`chargebacks` AS `chargebacks`,`t`.`signups` AS `signups`,
`t`.`frauds` AS `frauds`,`t`.`sales_type1` AS `sales_type1`,
`t`.`sales_type2` AS `sales_type2`,`t`.`sales_type3` AS `sales_type3`,
`t`.`sales_type4` AS `sales_type4`,`t`.`sales_type5` AS `sales_type5`,
`t`.`sales_type6` AS `sales_type6`,`t`.`sales_type7` AS `sales_type7`,
`t`.`sales_type8` AS `sales_type8`,`t`.`sales_type9` AS `sales_type9`,
`t`.`sales_type10` AS `sales_type10`,
((((((((((`t`.`sales_type1` + `t`.`sales_type2`) + `t`.`sales_type3`) + `t`.`sales_type4`) + `t`.`sales_type5`) + `t`.`sales_type6`) + `t`.`sales_type7`) + `t`.`sales_type8`) + `t`.`sales_type9`) + `t`.`sales_type10`) - `t`.`chargebacks`) AS `net`,
((((((((((`t`.`sales_type1` * (case when isnull(`t`.`sales_type1_payout`) then 0 else `t`.`sales_type1_payout` end)) + (`t`.`sales_type2` * (case when isnull(`t`.`sales_type2_payout`) then 0 else `t`.`sales_type2_payout` end))) + (`t`.`sales_type3` * (case when isnull(`t`.`sales_type3_payout`) then 0 else `t`.`sales_type3_payout` end))) + (`t`.`sales_type4` * (case when isnull(`t`.`sales_type4_payout`) then 0 else `t`.`sales_type4_payout` end))) + (`t`.`sales_type5` * (case when isnull(`t`.`sales_type5_payout`) then 0 else `t`.`sales_type5_payout` end))) + (`t`.`sales_type6` * (case when isnull(`t`.`sales_type6_payout`) then 0 else `t`.`sales_type6_payout` end))) + (`t`.`sales_type7` * (case when isnull(`t`.`sales_type7_payout`) then 0 else `t`.`sales_type7_payout` end))) + (`t`.`sales_type8` * (case when isnull(`t`.`sales_type8_payout`) then 0 else `t`.`sales_type8_payout` end))) + (`t`.`sales_type9` * (case when isnull(`t`.`sales_type9_payout`) then 0 else `t`.`sales_type9_payout` end))) + (`t`.`sales_type10` * (case when isnull(`t`.`sales_type10_payout`) then 0 else `t`.`sales_type10_payout` end))) AS `payouts`,
((((((((((`t`.`sales_type1` * (case when isnull(`t`.`sales_type1_earning`) then 0 else `t`.`sales_type1_earning` end)) + (`t`.`sales_type2` * (case when isnull(`t`.`sales_type2_earning`) then 0 else `t`.`sales_type2_earning` end))) + (`t`.`sales_type3` * (case when isnull(`t`.`sales_type3_earning`) then 0 else `t`.`sales_type3_earning` end))) + (`t`.`sales_type4` * (case when isnull(`t`.`sales_type4_earning`) then 0 else `t`.`sales_type4_earning` end))) + (`t`.`sales_type5` * (case when isnull(`t`.`sales_type5_earning`) then 0 else `t`.`sales_type5_earning` end))) + (`t`.`sales_type6` * (case when isnull(`t`.`sales_type6_earning`) then 0 else `t`.`sales_type6_earning` end))) + (`t`.`sales_type7` * (case when isnull(`t`.`sales_type7_earning`) then 0 else `t`.`sales_type7_earning` end))) + (`t`.`sales_type8` * (case when isnull(`t`.`sales_type8_earning`) then 0 else `t`.`sales_type8_earning` end))) + (`t`.`sales_type9` * (case when isnull(`t`.`sales_type9_earning`) then 0 else `t`.`sales_type9_earning` end))) + (`t`.`sales_type10` * (case when isnull(`t`.`sales_type10_earning`) then 0 else `t`.`sales_type10_earning` end))) AS `earnings`,
`t`.`run_id` AS `run_id`,`t`.`group_by` AS `group_by` 
from ((((`t_stats` `t` join `accounts` `a`) join `agents` `b`) join `companies` `c`) join `sites` `s`) 
where ((`t`.`agentid` = `a`.`id`) and (`a`.`id` = `b`.`id`) and (`b`.`companyid` = `c`.`id`) and (`t`.`siteid` = `s`.`id`)) ;
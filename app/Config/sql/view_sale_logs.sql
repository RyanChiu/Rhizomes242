CREATE VIEW `view_sale_logs` AS 
select companies.id as comid, agents.id as agid, convert(stats.trxtime, date) as date, agents.email, companies.officename as office, agents.ag1stname as agent, sites.sitename as site, types.typename as link, fees.earning, fees.price as payout, stats.transactionid as clickid
from stats, sites, types, fees, companies, agents
where stats.sales_number = 1 and stats.siteid = sites.id and stats.typeid = types.id and types.id = fees.typeid and stats.companyid = companies.id and stats.agentid = agents.id
-- --------------------------------------------------------

--
-- Table structure for table `t_stats`
--

CREATE TABLE `t_stats` (
  `trxtime` date NOT NULL,
  `agentid` int(11) NOT NULL,
  `companyid` int(11) NOT NULL,
  `siteid` int(11) NOT NULL,
  `raws` int(11) NOT NULL,
  `uniques` int(11) NOT NULL,
  `chargebacks` int(11) NOT NULL,
  `signups` int(11) NOT NULL,
  `frauds` int(11) NOT NULL,
  `sales_type1` int(11) NOT NULL,
  `sales_type2` int(11) DEFAULT '0',
  `sales_type3` int(11) DEFAULT '0',
  `sales_type4` int(11) DEFAULT '0',
  `sales_type5` int(11) DEFAULT '0',
  `sales_type6` int(11) DEFAULT '0',
  `sales_type7` int(11) DEFAULT '0',
  `sales_type8` int(11) DEFAULT '0',
  `sales_type9` int(11) DEFAULT '0',
  `sales_type10` int(11) DEFAULT '0',
  `sales_type1_payout` int(11) DEFAULT '0',
  `sales_type1_earning` int(11) DEFAULT '0',
  `sales_type2_payout` int(11) DEFAULT '0',
  `sales_type2_earning` int(11) DEFAULT '0',
  `sales_type3_payout` int(11) DEFAULT '0',
  `sales_type3_earning` int(11) DEFAULT '0',
  `sales_type4_payout` int(11) DEFAULT '0',
  `sales_type4_earning` int(11) DEFAULT '0',
  `sales_type5_payout` int(11) DEFAULT '0',
  `sales_type5_earning` int(11) DEFAULT '0',
  `sales_type6_payout` int(11) DEFAULT '0',
  `sales_type6_earning` int(11) DEFAULT '0',
  `sales_type7_payout` int(11) DEFAULT '0',
  `sales_type7_earning` int(11) DEFAULT '0',
  `sales_type8_payout` int(11) DEFAULT '0',
  `sales_type8_earning` int(11) DEFAULT '0',
  `sales_type9_payout` int(11) DEFAULT '0',
  `sales_type9_earning` int(11) DEFAULT '0',
  `sales_type10_payout` int(11) DEFAULT '0',
  `sales_type10_earning` int(11) DEFAULT '0',
  `run_id` int(11) NOT NULL,
  `group_by` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin;


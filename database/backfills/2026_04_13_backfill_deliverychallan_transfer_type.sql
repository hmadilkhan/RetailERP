-- Preview rows that still need transfer_type.
SELECT DC_id, DC_No, Transfer_id, branch_from, branch_to, date
FROM deliverychallan_general_details
WHERE transfer_type IS NULL OR transfer_type = '';

-- Backfill simple cases where the transfer id exists in only one parent table.
UPDATE deliverychallan_general_details dc
LEFT JOIN transfer_general_details tg
    ON tg.transfer_id = dc.Transfer_id
LEFT JOIN transfer_without_demand twd
    ON twd.transfer_id = dc.Transfer_id
SET dc.transfer_type = CASE
    WHEN tg.transfer_id IS NOT NULL AND twd.transfer_id IS NULL THEN 'general'
    WHEN tg.transfer_id IS NULL AND twd.transfer_id IS NOT NULL THEN 'without_demand'
    ELSE dc.transfer_type
END
WHERE dc.transfer_type IS NULL OR dc.transfer_type = '';

-- Preview rows that are still ambiguous because the same transfer id exists in both tables.
SELECT dc.DC_id, dc.DC_No, dc.Transfer_id, dc.branch_from, dc.branch_to, dc.date
FROM deliverychallan_general_details dc
INNER JOIN transfer_general_details tg
    ON tg.transfer_id = dc.Transfer_id
INNER JOIN transfer_without_demand twd
    ON twd.transfer_id = dc.Transfer_id
WHERE dc.transfer_type IS NULL OR dc.transfer_type = '';

-- Resolve common cases by matching branch_to with the general transfer.
UPDATE deliverychallan_general_details dc
INNER JOIN transfer_general_details tg
    ON tg.transfer_id = dc.Transfer_id
SET dc.transfer_type = 'general'
WHERE (dc.transfer_type IS NULL OR dc.transfer_type = '')
  AND dc.branch_to = tg.branch_to;

-- Resolve common cases by matching branch_to with the direct transfer.
UPDATE deliverychallan_general_details dc
INNER JOIN transfer_without_demand twd
    ON twd.transfer_id = dc.Transfer_id
SET dc.transfer_type = 'without_demand'
WHERE (dc.transfer_type IS NULL OR dc.transfer_type = '')
  AND dc.branch_to = twd.branch_to;

-- Final review: any remaining rows should be checked manually before updating.
SELECT DC_id, DC_No, Transfer_id, transfer_type, branch_from, branch_to, date
FROM deliverychallan_general_details
WHERE transfer_type IS NULL OR transfer_type = '';

DELETE Address
FROM
`addresses` Address
LEFT JOIN `addresses_organizations` AO ON( Address.address_id = AO.address_id)
LEFT JOIN `addresses_users` AU ON (Address.address_id = AU.address_id)
LEFT JOIN `addresses_events` AE ON (Address.address_id = AE.address_id)
WHERE AO.address_id IS NULL
AND AU.address_id IS NULL
AND AE.address_id IS NULL;
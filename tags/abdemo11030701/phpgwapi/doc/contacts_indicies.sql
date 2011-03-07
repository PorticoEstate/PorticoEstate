#
# This is an unsupported fix for the contacts backend in phpGW 0.9.16
# The script will create the missing indicies.  The script has been tested
# on mysql and pgsql.  Backup your database before running the script.
#
# Problems with this script should be emailed to alex@co.com.mx
#

CREATE INDEX owner_phpgw_contact_idx ON phpgw_contact (owner);
CREATE INDEX access_phpgw_contact_idx ON phpgw_contact  (access);
CREATE INDEX contact_type_id_phpgw_contact_idx ON phpgw_contact  (contact_type_id);
CREATE INDEX contact_id_cat_id_contact_type_id_phpgw_contact_idx ON phpgw_contact  (contact_id, cat_id, contact_type_id);
CREATE INDEX person_id_phpgw_contact_person_idx ON phpgw_contact_person  (person_id);
CREATE INDEX org_id_phpgw_contact_org_idx ON phpgw_contact_org  (org_id);
CREATE INDEX active_phpgw_contact_org_idx ON phpgw_contact_org  (active);
CREATE INDEX addr_id_phpgw_contact_org_person_idx ON phpgw_contact_org_person  (addr_id);
CREATE INDEX person_id_phpgw_contact_org_person_idx ON phpgw_contact_org_person  (person_id);
CREATE INDEX org_id_phpgw_contact_org_person_idx ON phpgw_contact_org_person  (org_id);
CREATE INDEX preferred_phpgw_contact_org_person_idx ON phpgw_contact_org_person  (preferred);
CREATE INDEX person_id_org_id_phpgw_contact_org_person_idx ON phpgw_contact_org_person  (person_id, org_id);
CREATE INDEX contact_id_phpgw_contact_addr_idx ON phpgw_contact_addr  (contact_id);
CREATE INDEX addr_type_id_phpgw_contact_addr_idx ON phpgw_contact_addr  (addr_type_id);
CREATE INDEX preferred_phpgw_contact_addr_idx ON phpgw_contact_addr  (preferred);
CREATE INDEX contact_id_phpgw_contact_note_idx ON phpgw_contact_note  (contact_id);
CREATE INDEX note_type_id_phpgw_contact_note_idx ON phpgw_contact_note  (note_type_id);
CREATE INDEX contact_id_phpgw_contact_others_idx ON phpgw_contact_others  (contact_id);
CREATE INDEX contact_owner_phpgw_contact_others_idx ON phpgw_contact_others  (contact_owner);
CREATE INDEX other_name_phpgw_contact_others_idx ON phpgw_contact_others  (other_name);
CREATE INDEX comm_data_phpgw_contact_comm_idx ON phpgw_contact_comm  (comm_data);
CREATE INDEX preferred_phpgw_contact_comm_idx ON phpgw_contact_comm  (preferred);
CREATE INDEX comm_descr_id_phpgw_contact_comm_idx ON phpgw_contact_comm  (comm_descr_id);
CREATE INDEX contact_id_phpgw_contact_comm_idx ON phpgw_contact_comm  (contact_id);
CREATE INDEX comm_id_contact_id_comm_descr_id_phpgw_contact_comm_idx ON phpgw_contact_comm  (comm_id, contact_id, comm_descr_id);
CREATE INDEX descr_phpgw_contact_comm_descr_idx ON phpgw_contact_comm_descr  (descr);
CREATE INDEX comm_type_id_phpgw_contact_comm_descr_idx ON phpgw_contact_comm_descr  (comm_type_id);
CREATE INDEX type_phpgw_contact_comm_type_idx ON phpgw_contact_comm_type  (type);
CREATE INDEX active_phpgw_contact_comm_type_idx ON phpgw_contact_comm_type  (active);
CREATE INDEX class_phpgw_contact_comm_type_idx ON phpgw_contact_comm_type  (class);
CREATE INDEX contact_type_descr_phpgw_contact_types_idx ON phpgw_contact_types  (contact_type_descr);

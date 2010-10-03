-- $Id: drop_tables.pgsql 10395 2002-06-10 13:01:47Z ldw $

drop table phpgw_inv_products;
drop sequence phpgw_inv_products_con_seq;

drop table phpgw_inv_statuslist;
drop sequence phpgw_inv_statusl_status_id_seq;

drop table phpgw_inv_orders;
drop sequence phpgw_inv_orders_id_seq;

drop table phpgw_inv_orderpos;
drop sequence phpgw_inv_orderpos_id_seq;

drop table phpgw_inv_delivery;
drop sequence phpgw_inv_delivery_id_seq;

drop table phpgw_inv_deliverypos;
drop sequence phpgw_inv_deliverypos_id_seq;

drop table phpgw_inv_invoice;
drop sequence phpgw_inv_invoice_id_seq;

drop table phpgw_inv_invoicepos;
drop sequence phpgw_inv_invoicepos_id_seq;

drop table phpgw_inv_stockrooms;
drop sequence phpgw_inv_stockrooms_id_seq;

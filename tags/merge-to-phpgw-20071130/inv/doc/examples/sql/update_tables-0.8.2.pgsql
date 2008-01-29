-- $Id: update_tables-0.8.2.pgsql 10395 2002-06-10 13:01:47Z ldw $

alter table inv_products rename as phpgw_inv_products;
alter table inv_dist rename as phpgw_inv_dist;
alter table inv_categorys rename as phpgw_inv_categorys;
alter table inv_status_list rename as phpgw_inv_status_list;
alter table inv_orders rename as phpgw_inv_orders;
alter table inv_orderpos rename as phpgw_inv_orderpos;
alter table inv_delivery rename as phpgw_inv_delivery;
alter table inv_deliverypos rename as phpgw_inv_deliverypos;
alter table inv_invoice rename as phpgw_inv_invoice;
alter table inv_invoicepos rename as phpgw_inv_invoicepos;

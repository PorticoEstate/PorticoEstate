-- $Id: drop_tables-0.8.1.pgsql 10395 2002-06-10 13:01:47Z ldw $ 

drop table inv_products;
drop sequence inv_products_con_seq;

drop table inv_dist;
drop sequence inv_dist_con_seq;

drop table inv_categorys;
drop sequence inv_categorys_con_seq;

drop table inv_status_list;
drop sequence inv_status_list_status_id_seq;

drop table inv_orders;
drop sequence inv_orders_id_seq;

drop table inv_orderpos;
drop sequence inv_orderpos_id_seq;

drop table inv_delivery;
drop sequence inv_delivery_id_seq;

drop table inv_deliverypos;
drop sequence inv_deliverypos_id_seq;

drop table inv_invoice;
drop sequence inv_invoice_id_seq;

drop table inv_invoicepos;
drop sequence inv_invoicepos_id_seq;
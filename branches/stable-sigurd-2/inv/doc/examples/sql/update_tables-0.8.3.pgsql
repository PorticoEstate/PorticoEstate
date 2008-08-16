-- $Id: update_tables-0.8.3.pgsql 10395 2002-06-10 13:01:47Z ldw $ 

	drop sequence phpgw_inv_categorys_con_seq;
	drop table phpgw_inv_categorys;

------------------------------------------------------------
--             02/07/2001  0.8.3pre1                      --
------------------------------------------------------------

	create table temp as select * from phpgw_inv_products;
	drop sequence phpgw_inv_products_con_seq;
	drop table phpgw_inv_products;

	CREATE TABLE phpgw_inv_products (
		con			serial,
		id			varchar(20) NOT NULL,
		serial		varchar(64) NOT NULL,
		name		varchar(255) NOT NULL,
		descr		text,
		category	int,
		status		int,
		weight		int,
		cost		decimal(10,2),
		price		decimal(10,2),
		retail		decimal(10,2),
		stock		int,
		mstock		int,
		url			varchar(255) NOT NULL,
		ftp			varchar(255) NOT NULL,
		dist		int,
		pdate		int,
		sdate		int
	);

	CREATE INDEX phpgw_inv_products_key ON phpgw_inv_products(con,id);

	insert into phpgw_inv_products select * from temp;
	drop table temp;

------------------------------------------------------------
--             02/16/2001  0.8.3pre2                      --
------------------------------------------------------------

	create table temp as select * from phpgw_inv_orders;
	drop sequence phpgw_inv_orders_id_seq;
	drop table phpgw_inv_orders;

	CREATE TABLE phpgw_inv_orders (
		id			serial,
		owner		int,
		access		varchar(7),
		num			varchar(20) NOT NULL,
		date		int,
		customer	int,
		descr		text
    );

	CREATE INDEX phpgw_inv_orders_key ON phpgw_inv_orders(id,num);

	insert into phpgw_inv_orders select * from temp;
	drop table temp;
--
	create table temp as select * from phpgw_inv_orderpos;
	drop sequence phpgw_inv_orderpos_id_seq;
	drop table phpgw_inv_orderpos;

	CREATE TABLE phpgw_inv_orderpos (
		id			serial,
		order_id	int,
		product_id	int,
		piece		int,
		tax			decimal(6,2)
	);

	insert into phpgw_inv_orderpos select * from temp;
	drop table temp;
--
	create table temp as select * from phpgw_inv_deliverypos;
	drop sequence phpgw_inv_deliverypos_id_seq;
	drop table phpgw_inv_deliverypos;

	CREATE TABLE phpgw_inv_deliverypos (
		id			serial,
		delivery_id	int,
		product_id	int
	);

	insert into phpgw_inv_deliverypos select * from temp;
	drop table temp;
--
	create table temp as select * from phpgw_inv_invoicepos;
	drop sequence phpgw_inv_invoicepos_id_seq;
	drop table phpgw_inv_invoicepos;

	CREATE TABLE phpgw_inv_invoicepos (
		id			serial,
		invoice_id	int,
		product_id	int
	);

	insert into phpgw_inv_invoicepos select * from temp;
	drop table temp;
--
	create table temp as select * from phpgw_inv_invoice;
	drop sequence phpgw_inv_invoice_id_seq;
    drop table phpgw_inv_invoice;

	CREATE TABLE phpgw_inv_invoice (
		id			serial,
		num			varchar(20) NOT NULL,
		date		int,
		order_id	int,
		sum			decimal(20,2)
	);

	CREATE INDEX phpgw_inv_invoice_key ON phpgw_inv_invoice(id,num);

	insert into phpgw_inv_invoice select * from temp;
	drop table temp;
--
    create table temp as select * from phpgw_inv_delivery;
    drop sequence phpgw_inv_delivery_id_seq;
	drop table phpgw_inv_delivery;

	CREATE TABLE phpgw_inv_delivery (
		id			serial,
		num			varchar(20) NOT NULL,
		date		int,
		order_id	int,
		sum			decimal(20,2)
	);

	CREATE INDEX phpgw_inv_delivery_key ON phpgw_inv_delivery(id,num);

	insert into phpgw_inv_delivery select * from temp;
	drop table temp;

------------------------------------------------------
--             02/21/2001 0.8.3pre3                 --
------------------------------------------------------

	drop sequence phpgw_inv_dist_con_seq;
	drop table phpgw_inv_dist;

------------------------------------------------------
--             03/04/2001 0.8.3pre4                 --
------------------------------------------------------

-- please repeat the pre1_update --

	insert into phpgw_inv_statuslist (status_name) values ('saled');

------------------------------------------------------
--                   03/04/2001 0.8.3pre5           --  
-----------------------------------------------------

	insert into phpgw_inv_statuslist (status_name) values ('archive');
	alter table phpgw_inv_orders add column status text check(status in('open','closed','archive')) DEFAULT 'open' NOT NULL;

------------------------------------------------------
--             03/04/2001 0.8.3pre6                 --
------------------------------------------------------

-- please repeat the pre2_update to add access field to phpgw_inv_orders table --

------------------------------------------------------
--             03/04/2001 0.8.3.007                 --
------------------------------------------------------

	alter table phpgw_inv_products add column bin int;

	CREATE TABLE phpgw_inv_stockrooms (
		id			serial,
		room_owner	int,
		room_access	varchar(7),
		room_name	varchar(255) NOT NULL,
		room_note	text
	);

	alter table phpgw_inv_products add column product_note text;
	delete from phpgw_inv_statuslist where status_name='saled';
	insert into phpgw_inv_statuslist (status_name) values ('sold');

-- ###############################################################                                                                                                                           
-- #                   03/04/2001 0.8.3.008                      #                                                                                                                           
-- ###############################################################                                                                                                                           

	CREATE UNIQUE INDEX order_num ON phpgw_inv_orders(num);
	CREATE UNIQUE INDEX invoice_num ON phpgw_inv_invoice(num);
	CREATE UNIQUE INDEX delivery_num ON phpgw_inv_delivery(num);

-- $Id: create_tables.pgsql 10395 2002-06-10 13:01:47Z ldw $

	CREATE TABLE phpgw_inv_products (
		con				serial,
		id				varchar(20) NOT NULL,
		serial			varchar(64) NOT NULL,
		name			varchar(255) NOT NULL,
		descr			text,
		category		int,
		status			int,
		weight			int,
		cost			decimal(10,2),
		price			decimal(10,2),
		retail			decimal(10,2),
		stock			int,
		mstock			int,
		url				varchar(255) NOT NULL,
		ftp				varchar(255) NOT NULL,
		dist			int,
		pdate			int,
		sdate			int,
		bin				int,
		product_note	text
	);

	CREATE INDEX phpgw_inv_products_key ON phpgw_inv_products(con,id);

	CREATE table phpgw_inv_statuslist (
		status_id	serial,
		status_name	varchar(255) NOT NULL
	);

	CREATE TABLE phpgw_inv_orders (
		id			serial,
		owner		int,
		access		varchar(7),
		num			varchar(20) NOT NULL,
		date		int,
		customer	int,
		descr		text,
		status		text check(status in('open','closed','archive')) DEFAULT 'open' NOT NULL
	);

	CREATE INDEX phpgw_inv_orders_key ON phpgw_inv_orders(id,num);
	CREATE UNIQUE INDEX order_num ON phpgw_inv_orders(num);

	CREATE TABLE phpgw_inv_orderpos (
		id			serial,
		order_id	int,
		product_id	int,
		piece		int,
		tax			decimal(6,2)
	);

	CREATE TABLE phpgw_inv_delivery (
		id			serial,
		num			varchar(20) NOT NULL,
		date		int,
		order_id	int
	);

	CREATE INDEX phpgw_inv_delivery_key ON phpgw_inv_delivery(id,num);
	CREATE UNIQUE INDEX delivery_num ON phpgw_inv_delivery(num);

	CREATE TABLE phpgw_inv_deliverypos (
		id			serial,
		delivery_id	int,
		product_id	int
	);

	CREATE TABLE phpgw_inv_invoice (
		id			serial,
		num			varchar(20) NOT NULL,
		date		int,
		order_id	int,
		sum			decimal(20,2)
	);

	CREATE INDEX phpgw_inv_invoice_key ON phpgw_inv_invoice(id,num);
	CREATE UNIQUE INDEX invoice_num ON phpgw_inv_invoice(num);

	CREATE TABLE phpgw_inv_invoicepos (
		id			serial,
		invoice_id	int,
		product_id	int
	);

	CREATE TABLE phpgw_inv_stockrooms (
		id			serial,
		room_owner	int,
		room_access	varchar(7),
		room_name	varchar(255) NOT NULL,
		room_note	text
	);

	insert into phpgw_inv_statuslist (status_name) values ('available');
	insert into phpgw_inv_statuslist (status_name) values ('no longer available');
	insert into phpgw_inv_statuslist (status_name) values ('back order');
	insert into phpgw_inv_statuslist (status_name) values ('unknown');
	insert into phpgw_inv_statuslist (status_name) values ('other');
	insert into phpgw_inv_statuslist (status_name) values ('sold');
	insert into phpgw_inv_statuslist (status_name) values ('archive');

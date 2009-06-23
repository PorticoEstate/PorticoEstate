-- $Id: update_tables-0.8.1.pgsql 10395 2002-06-10 13:01:47Z ldw $ 

---------------------------------------------------
--              update 11/29/2000                --
---------------------------------------------------          
alter table inv_categorys add column tax decimal(6,2);

---------------------------------------------------
--             update 12/02/2000                 --                                                                                                                                          
---------------------------------------------------
CREATE TABLE inv_orders (                                                                                                                                                                    
        id              serial,                                                                                                                                     
        num             varchar(11),                                                                                                                                                
        date            int,                                                                                                                                                             
        customer        int,                                                                                                                                                             
        descr           text                                                                                                                                                                
);                                                                                                                                                                                           
                                                                                                                                                                                             
CREATE TABLE inv_orderpos (                                                                                                                                                                  
        id              serial,                                                                                                                                     
        order_id        varchar(11),                                                                                                                                                
        product_id      varchar(11)                                                                                                                                                         
);

---------------------------------------------------
--             update 12/04/2000                 --                                                                                                    
---------------------------------------------------
CREATE TABLE inv_delivery (                                                                                                                                                                  
        id              serial,                                                                                                                                     
        num             varchar(11),                                                                                                                                                
        date            int,                                                                                                                                                             
        order_id        varchar(11)                                                                                                                                                         
);                                                                                                                                                                                           
                                                                                                                                                                                             
---------------------------------------------------
--             update 12/09/2000                 --                                                                                                                                       
---------------------------------------------------
alter table inv_orderpos add column tax decimal(6,2);

CREATE TABLE inv_invoice (                                                                                                                                                       
        id              serial,                                                                                                                                                  
        num             varchar(11),                                                                                                                                             
        date            int,                                                                                                                                                     
        order_id        varchar(11),                                                                                                                                             
        sum             decimal(20,2)                                                                                                                                            
);

---------------------------------------------------
--             update 12/11/2000                 --                                                                                                                                       
---------------------------------------------------
CREATE TABLE inv_deliverypos (                                                                                                                                                            
        id              serial,                                                                                                                                                         
        delivery_id     varchar(11) NOT NULL,                                                                                                                                           
        product_id      varchar(11)                                                                                                                                                            
);

---------------------------------------------------
--             update 12/22/2000                 --
---------------------------------------------------
alter table inv_orders modify column num varchar(11) NOT NULL;
alter table inv_orderpos modify column order_id varchar(11) NOT NULL;

alter table inv_delivery modify column num varchar(11) NOT NULL;
alter table inv_deliverypos modify column delivery_id varchar(11) NOT NULL;

alter table inv_invoice modify column num varchar(11) NOT NULL;

create unique index orders_num on inv_orders (num);                                                                                                                                              
create unique index delivery_num on inv_delivery (num);                                                                                                                                          
create unique index invoice_num on inv_invoice (num);

---------------------------------------------------
--             update 12/28/2000                 --                                                                                                                                                   
---------------------------------------------------
CREATE TABLE inv_invoicepos (                                                                                                                                                                         
   id          serial,                                                                                                                                                                                
   invoice_id  varchar(11) NOT NULL,                                                                                                                                                                  
   product_id  varchar(11)                                                                                                                                                                            
);

---------------------------------------------------
--             update 12/30/2000                 --                                                                                                                                                
---------------------------------------------------
alter table inv_products modify column category varchar(3);

----------------------------------------------------
--             update 01/01/2001                  --                                                                                                                                        
----------------------------------------------------
alter table inv_categorys add column level int default '0' NOT NULL;                                                                                                                    
alter table inv_categorys add column par_cat varchar(3);                                                                                                                                   
alter table inv_categorys add column main_cat varchar(3);
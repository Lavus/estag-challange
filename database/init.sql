--Every command here will be executed when the container is created

CREATE TABLE IF NOT EXISTS CATEGORIES (
    CODE SERIAL,
    NAME VARCHAR(4000) NOT NULL,
    TAX VARCHAR(4000) NOT NULL,
    CONSTRAINT PK_CATEGORIES PRIMARY KEY (CODE)
);

CREATE TABLE IF NOT EXISTS PRODUCTS (
    CODE SERIAL,
    NAME VARCHAR(4000) NOT NULL,
    AMOUNT VARCHAR(4000) NOT NULL,
    PRICE VARCHAR(4000) NOT NULL,
    CATEGORY_CODE BIGINT NOT NULL,
    CONSTRAINT PK_PRODUCTS PRIMARY KEY (CODE),
    CONSTRAINT FK_CATEGORY FOREIGN KEY (CATEGORY_CODE) REFERENCES CATEGORIES(CODE)
);

CREATE TABLE IF NOT EXISTS ORDERS (
    CODE SERIAL,
    VALUE_TOTAL VARCHAR(4000) NOT NULL,
    VALUE_TAX VARCHAR(4000) NOT NULL,
    CONSTRAINT PK_ORDERS PRIMARY KEY (CODE)
);

CREATE TABLE IF NOT EXISTS ORDER_ITEM(
    CODE SERIAL,
    ORDER_CODE BIGINT NOT NULL,
    PRODUCT_CODE BIGINT,
    PRODUCT_NAME VARCHAR(4000) NOT NULL,
    AMOUNT VARCHAR(4000) NOT NULL,
    PRICE VARCHAR(4000) NOT NULL,
    TAX VARCHAR(4000) NOT NULL,
    CONSTRAINT PK_ORDER_ITEM PRIMARY KEY (CODE),
    CONSTRAINT FK_ORDER_ITEM_PRODUCT FOREIGN KEY (PRODUCT_CODE) REFERENCES PRODUCTS(CODE),
    CONSTRAINT FK_ORDER_ITEM_ORDER FOREIGN KEY (ORDER_CODE) REFERENCES ORDERS(CODE)
);

CREATE TABLE IF NOT EXISTS LOG_CATEGORIES (
    CODE BIGINT NOT NULL,
    NAME VARCHAR(4000) NOT NULL,
    TAX VARCHAR(4000) NOT NULL,
    CONSTRAINT PK_LOG_CATEGORIES PRIMARY KEY (CODE)
);

CREATE TABLE IF NOT EXISTS LOG_PRODUCTS (
    CODE BIGINT NOT NULL,
    NAME VARCHAR(4000) NOT NULL,
    AMOUNT VARCHAR(4000) NOT NULL,
    PRICE VARCHAR(4000) NOT NULL,
    CATEGORY_CODE BIGINT NOT NULL,
    CONSTRAINT PK_LOG_PRODUCTS PRIMARY KEY (CODE)
);

CREATE TABLE IF NOT EXISTS LOG_ORDERS (
    CODE BIGINT NOT NULL,
    VALUE_TOTAL VARCHAR(4000) NOT NULL,
    VALUE_TAX VARCHAR(4000) NOT NULL,
    CONSTRAINT PK_LOG_ORDERS PRIMARY KEY (CODE)
);

CREATE TABLE IF NOT EXISTS LOG_ORDER_ITEM(
    CODE BIGINT NOT NULL,
    ORDER_CODE BIGINT NOT NULL,
    PRODUCT_CODE BIGINT,
    PRODUCT_NAME VARCHAR(4000) NOT NULL,
    AMOUNT VARCHAR(4000) NOT NULL,
    PRICE VARCHAR(4000) NOT NULL,
    TAX VARCHAR(4000) NOT NULL,
    CONSTRAINT PK_LOG_ORDER_ITEM PRIMARY KEY (CODE)
);

CREATE OR REPLACE FUNCTION autodeleteproducts() RETURNS TRIGGER AS $deleteproduct$
   BEGIN
      DELETE FROM public.products WHERE CATEGORY_CODE = OLD.code;
      RETURN OLD;
   END;
$deleteproduct$ LANGUAGE plpgsql;

CREATE TRIGGER autodeleteproducts_trigger BEFORE DELETE ON categories
FOR EACH ROW EXECUTE PROCEDURE autodeleteproducts();

CREATE OR REPLACE FUNCTION autoremovecartproducts() RETURNS TRIGGER AS $deleteproductcart$
	BEGIN
		UPDATE order_item SET product_code = NULL WHERE order_item.product_code = OLD.code;
		RETURN OLD;
	END;
$deleteproductcart$ LANGUAGE plpgsql;

CREATE TRIGGER autoremovecartproducts_trigger BEFORE DELETE ON products
FOR EACH ROW EXECUTE PROCEDURE autoremovecartproducts();

CREATE OR REPLACE FUNCTION autoremovecartproductsofcategory() RETURNS TRIGGER AS $deleteproductscartofcategory$
	BEGIN
		UPDATE order_item SET product_code = NULL WHERE order_item.product_code IN ( SELECT products.code FROM products WHERE products.category_code = NEW.code );
		RETURN NEW;
	END;
$deleteproductscartofcategory$ LANGUAGE plpgsql;

CREATE TRIGGER autoremovecartproductsofcategory_trigger AFTER UPDATE OF tax ON categories
FOR EACH ROW EXECUTE PROCEDURE autoremovecartproductsofcategory();

CREATE OR REPLACE FUNCTION autoremovecartproductofupdate() RETURNS TRIGGER AS $deleteproductscartofupdate$
	BEGIN
		UPDATE order_item SET product_code = NULL WHERE order_item.product_code = NEW.code;
		RETURN NEW;
	END;
$deleteproductscartofupdate$ LANGUAGE plpgsql;

CREATE TRIGGER autoremovecartproductofupdate_trigger AFTER UPDATE OF name, price, category_code ON products
FOR EACH ROW EXECUTE PROCEDURE autoremovecartproductofupdate();

CREATE OR REPLACE FUNCTION autoremoveoldids() RETURNS TRIGGER AS $autoremoveoldids$
	BEGIN
		UPDATE order_item SET product_code = NULL WHERE order_item.order_code IN ( SELECT MAX( orders.code ) FROM orders );
		RETURN NEW;
	END;
$autoremoveoldids$ LANGUAGE plpgsql;

CREATE TRIGGER autoremoveoldids_trigger BEFORE INSERT ON orders
FOR EACH ROW EXECUTE PROCEDURE autoremoveoldids();

CREATE OR REPLACE FUNCTION autologcategories() RETURNS TRIGGER AS $autologcategories$
	BEGIN
		INSERT INTO log_categories (code, name, tax) VALUES (OLD.code, OLD.name, OLD.tax);
		RETURN OLD;
	END;
$autologcategories$ LANGUAGE plpgsql;

CREATE TRIGGER autologcategories_trigger BEFORE DELETE ON categories
FOR EACH ROW EXECUTE PROCEDURE autologcategories();

CREATE OR REPLACE FUNCTION autologproducts() RETURNS TRIGGER AS $autologproducts$
	BEGIN
		INSERT INTO log_products (code, name, amount, price, category_code) VALUES (OLD.code, OLD.name, OLD.amount, OLD.price, OLD.category_code);
		RETURN OLD;
	END;
$autologproducts$ LANGUAGE plpgsql;

CREATE TRIGGER autologproducts_trigger BEFORE DELETE ON products
FOR EACH ROW EXECUTE PROCEDURE autologproducts();

CREATE OR REPLACE FUNCTION autologorders() RETURNS TRIGGER AS $autologorders$
	BEGIN
		INSERT INTO log_orders (code, value_total, value_tax) VALUES (OLD.code, OLD.value_total, OLD.value_tax);
		RETURN OLD;
	END;
$autologorders$ LANGUAGE plpgsql;

CREATE TRIGGER autologorders_trigger BEFORE DELETE ON orders
FOR EACH ROW EXECUTE PROCEDURE autologorders();

CREATE OR REPLACE FUNCTION autologorder_item() RETURNS TRIGGER AS $autologorder_item$
	BEGIN
		INSERT INTO log_order_item (code, order_code, product_code, product_name, amount, price, tax) VALUES (OLD.code, OLD.order_code, OLD.product_code, OLD.product_name, OLD.amount, OLD.price, OLD.tax);
		RETURN OLD;
	END;
$autologorder_item$ LANGUAGE plpgsql;

CREATE TRIGGER autologorder_item_trigger BEFORE DELETE ON order_item
FOR EACH ROW EXECUTE PROCEDURE autologorder_item();

CREATE OR REPLACE FUNCTION autodeleteorder_item() RETURNS TRIGGER AS $autodeleteorder_item$
   BEGIN
      DELETE FROM public.order_item WHERE order_code = OLD.code;
      RETURN OLD;
   END;
$autodeleteorder_item$ LANGUAGE plpgsql;

CREATE TRIGGER autodeleteorder_item_trigger BEFORE DELETE ON orders
FOR EACH ROW EXECUTE PROCEDURE autodeleteorder_item();


ALTER TABLE xlite_modules CHANGE version version varchar(12) NOT NULL DEFAULT '0';
INSERT INTO xlite_payment_methods VALUES ('payflowlink','Credit Card','Visa, Mastercard, American Express','payflowlink','a:3:{s:5:"login";s:0:"";s:7:"partner";s:0:"";s:11:"gateway_url";s:30:"https://payflowlink.paypal.com";}',10,0);

ALTER TABLE xlite_modules CHANGE version version varchar(12) NOT NULL DEFAULT '0';
INSERT INTO `xlite_payment_methods` VALUES ('chronopay', 'ChronoPay', 'Visa, Mastercard, American Express', 'chronopay', 'a:4:{s:10:"product_id";s:0:"";s:8:"language";s:2:"EN";s:3:"url";s:43:"https://secure.chronopay.com/index_shop.cgi";s:9:"secure_ip";s:11:"69.20.58.35";}', 50, 0);

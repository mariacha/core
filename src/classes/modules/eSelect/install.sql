ALTER TABLE xlite_modules CHANGE version version varchar(12) NOT NULL DEFAULT '0';
INSERT INTO xlite_payment_methods SET payment_method = 'eselect_cc', name = 'eSelect', details = 'Visa, Mastercard, American Express', orderby = '15', class = 'eselect_cc', enabled = 0, params = 'a:5:{s:8:"store_id";s:0:"";s:9:"api_token";s:0:"";s:10:"trans_type";s:5:"purch";s:8:"testmode";s:1:"Y";s:12:"order_prefix";s:3:"LC_";}';

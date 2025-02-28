# Novalnet payment plugin for Shopware 6
Novalnet payment plugin for Shopware 6 simplifies your daily work by automating the entire payment process, from checkout till collection. The Shopware 6 payment plugin is designed to help you increase your sales by offering various international and local payment methods.

## Why Shopware 6 with Novalnet? 
Shopware 6 is the next generation shop system based on advanced hook system. With the open source and commercial versions, users can easily create Shopware 6 storefronts based on Twig and Bootstrap technologies. Novalnet’s Shopware 6 payment plugin supports international and local payments including payment guarantee and cash payment particularly for Shopware. We encrypt our end customer data (strong encryption) to free you from data protection concerns and from payment related licenses.

## Advantages of Shopware 6 Payment plugin
-	Easy configuration for all payment methods - international and local
-	One platform for all payment types and related services
-	Complete automation of all payment processes
-	More than 50 fraud prevention modules integrated to prevent risk in real-time
-	Effortless configuration of risk management with fraud prevention
-	Comprehensive affiliate system with automated split conversion of transaction on revenue sharing
-	No PCI DSS certification required when using our payment module
-	Real-time monitoring of the payment methods & transaction flows 
-	Multilevel claims management with integrated handover to collection and various export functions for the accounting
-	Automated e-mail notification function concerning payment status reports
-	Clear real-time overview and monitoring of payment status
-	Automated bookkeeping report in XML, SOAP, CSV, MT940
-	Seamless and fast integration of the payment plugin
-	Secure SSL- encoded gateways
-	Seamless checkout Iframe integration
-	One click shopping supported Credit/Debit Cards & Direct Debit SEPA 
- 	Zero amount authorization supported for Credit/Debit Cards, Direct Debit SEPA, Direct Debit ACH, Apple Pay & Google Pay payments
-	Easy way of confirmation and cancellation of on-hold transactions for Direct Debit SEPA, Direct Debit SEPA with payment guarantee, Instalment payment for Direct Debit SEPA, Credit/Debit Cards, Invoice, Invoice with payment guarantee, Instalment payment for Invoice, Prepayment, PayPal, Apple Pay & Google Pay
-	Refund option for Credit/Debit Cards, Direct Debit SEPA, Direct Debit ACH, Direct Debit SEPA with payment guarantee, Instalment by Direct Debit SEPA, Invoice, Invoice with payment guarantee, Instalment by Invoice, Prepayment, Barzahlen/viacash, Sofort, iDEAL, eps, giropay, PayPal, MB Way, Przelewy24, PostFinance Card, PostFinance E-Finance, Bancontact, Apple Pay, Google Pay, Online bank transfer, Alipay, WeChat pay, Trustly, Blik, Payconiq & TWINT
-	Order creation process handled for shop admin for the Direct Debit SEPA, Invoice, Prepayment, Barzahlen/viacash & Multibanco
-	Responsive templates

## Compatibility
Shopware 6 payment plugin is compatible with below technical capabilities. 
- [x]	Shopware versions 6.6.0.0 - 6.6.10.1
- [x]	Linux based OS with Apache 2.2 or 2.4
- [x]	PHP 7.2.0 or higher
- [x]	MySQL 5.7 or higher

## Supported payment methods

-	Direct Debit SEPA
-	Direct Debit ACH
-	Credit/Debit Cards
-	Apple Pay
-	Google Pay
-	Invoice
-	Prepayment
-	Invoice with payment guarantee
-	Direct Debit SEPA with payment guarantee
-	Instalment by Invoice
-	Instalment by Direct Debit SEPA
-	iDEAL
-	Sofort
-	giropay
-	Barzahlen/viacash
-	Przelewy24
-	eps
-	PayPal
-	MB Way
-	PostFinance Card
-	PostFinance E-Finance
-	Bancontact
-	Multibanco
-	Online bank transfer
-	Alipay
-	WeChat Pay
-	Trustly
-	Blik
-	Payconiq
- TWINT

## Installation via Composer

#### Follow the below steps and run each command in your terminal from the shop root directory
 ##### 1. Run the below command to upload the payment plugin
 ```
 composer require novalnet/shopware6-payment
 ```
 ##### 2. Run the below command to refresh the payment plugin
 ```
 php bin/console plugin:refresh
 ```
 ##### 3. Run the below command to install and activate the payment plugin
 ```
 php bin/console plugin:install --activate --clearCache NovalnetPayment
 ```
## Quick Installation via plugin upload
Follow the below steps to install the payment plugin for Shopware 6 in the shop system.

1. **Download** the plugin from <a href="https://store.shopware.com/en/noval29035660349f/novalnet-payments-plugin.html"> here</a> or <a href="https://www.novalnet.de/kontakt/sales"> contact us</a>.

2. **Login** to shop backend, move to:
   - Extensions
     - My extensions
       - Upload extensions
       
3. **Upload** the Shopware 6 payment Apps from Novalnet to the shop

4. Click **Install** to install the Novalnet Shopware 6 Payment Apps

## Documentation & Support
For more information about the integration, please get in touch with us at sales@novalnet.de or +49 89 9230683-20 or by contacting us <a href="https://www.novalnet.de/kontakt/sales"> here.</a>

Novalnet AG<br>
Zahlungsinstitut (ZAG)<br>
Gutenbergstraße 7<br>
D-85748 Garching<br>
Deutschland<br>
E-mail: sales@novalnet.de<br>
Tel: +49 89 9230683-20<br>
Web: www.novalnet.de

## Licenses

As a European Payment institution, Novalnet holds all necessary payment licenses to accept and process payments worldwide. We also comply with European data protection regulations to guarantee advanced data protection worldwide.

See here for [Freeware License Agreement](https://www.novalnet.com/payment-plugins-free-license/).

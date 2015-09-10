# Spacemariachi Estafeta Shipping Method for Magento

Magento module that implements Estafeta as a shipping method, it implements availability of service, available services and prices and tracking information.

### Version
0.9.0

### Requirements

* [SoapClient] - May need to install the php_soap extension

### Installation

Copy the folder to you magento installation, or use modman

```sh
modman clone git@github.com:ftorresarr/magento_estafeta.git 
```

Since Estafeta's web services only use MXN to give rate pricing, unless you're using MXN as your only currency, you need to add it as an allowed currency and setup a conversion rate 

   1. Go to "System-> Configuration-> Currency Setup-> Currency options-> Allowed currencies" and add Mexican Peso to your selection
   2. Go to "System-> Manage Currency-> Rates", click Import and then Save, or setup a conversion rate manually

Out of the box you can select wheather quote pricing as an envelope or a packet, however since Estafeta requires to send the three dimensions of the packet as well as weight, you need to input a ficticious packet size to use. 

### Todos

 - Implement real weight from products
 - Translate configuration to English
 - Add Code Comments

License
----

GNU GENERAL PUBLIC LICENSE





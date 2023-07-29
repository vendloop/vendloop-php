#Sample calls

``` php

// initialize `$vendloop` object
$vendloop = new Vendloop([
  'api_key' => 'API_KEY'
]);

// Make a call to the resource/method

// list customer
$vendloop->customers();
$vendloop->customers->list();
$vendloop->customers->list([
  'limit'=>50,
  'start'=>2
]); // list the second page at 50 customers per page

// fetch customer
$vendloop->customers(12);
$vendloop->customers->fetch(12);
$vendloop->customers->fetch([
  'id'=>'12'
]);

// delete customer
$vendloop->customers->delete(12);
$vendloop->customers->delete([
  'id'=>'12'
]);

```

<?php

two_url('business_dashboard');
two_url('business_template_editor', ['id' => 123]);

two_config('database.main.password');

$r->get('business_dashboard', '/business/dashboard', 'Business\Dashboard::index');
$r->get('apiv1_pos_product', '/api/v1/pos/products/{upc:\d+}', 'Api\V1\Pos\Products::single');

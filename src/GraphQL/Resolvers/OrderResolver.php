<?php

namespace App\GraphQL\Resolvers;

use App\Model\Order;

class OrderResolver
{
    public function placeOrder(array $args): bool
    {
        $orderData = $args['order'];
        return Order::create($orderData);
    }
}

?>
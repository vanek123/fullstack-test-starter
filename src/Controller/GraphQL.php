<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

use App\GraphQL\Types\ProductType;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\OrderInputType;

use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Resolvers\OrderResolver;

class GraphQL {
    static public function handle() {
        try {
            $productType = new ProductType();
            $categoryType = new CategoryType();
            $orderInputType = new OrderInputType();

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::listOf($productType),
                        'args' => [
                            'category' => Type::string(),
                        ],
                        'resolve' => fn($root, $args) => (new ProductResolver())->resolve($args),
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => fn($root, $args) => (new ProductResolver())->getProduct($args),
                    ],
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => fn() => (new CategoryResolver())->resolve(),
                    ],
                ],
            ]);
        
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'placeOrder' => [
                        'type' => Type::boolean(),
                        'args' => [
                            'order' => Type::nonNull($orderInputType),
                        ],
                        'resolve' => fn($root, $args) => (new OrderResolver())->placeOrder($args),
                    ],
                ],
            ]);
        
            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
            );
        
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
        
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
        
            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            //$output = $result->toArray();
            $output = $result->toArray(\GraphQL\Error\DebugFlag::INCLUDE_DEBUG_MESSAGE | \GraphQL\Error\DebugFlag::INCLUDE_TRACE);
        } catch (Throwable $e) {
            $output = [
                'errors' => [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
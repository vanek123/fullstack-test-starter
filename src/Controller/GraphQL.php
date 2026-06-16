<?php

namespace App\Controller;

use App\Database;
use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Resolvers\OrderResolver;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\OrderInputType;
use App\GraphQL\Types\ProductType;
use App\Model\Category;
use App\Model\Order;
use App\Model\Repository\ProductRepository;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL
{
    public static function handle()
    {
        try {
            $pdo = Database::connect();
            $productRepository = new ProductRepository($pdo);
            $category = new Category($pdo);
            $order = new Order($pdo);

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
                        'resolve' => fn($root, $args) => (new ProductResolver($productRepository))->resolve($args),
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => fn($root, $args) => (new ProductResolver($productRepository))->getProduct($args),
                    ],
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => fn() => (new CategoryResolver($category))->resolve(),
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
                        'resolve' => fn($root, $args) => (new OrderResolver($order, $productRepository))->placeOrder($args),
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
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'errors' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
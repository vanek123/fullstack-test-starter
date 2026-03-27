<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

use App\Model\SimpleProduct;
use App\Model\DVD;
use App\Model\Furniture;
use App\Model\Book;

class GraphQL {
    static public function handle() {
        try {
            
            $attributeType = new ObjectType([
                'name' => 'Attribute',
                'fields' => [
                    'height' => [
                        'type' => Type::float(),
                        'resolve' => fn($attr) => $attr['height'] ?? null
                        ],
                    'width' => [
                        'type' => Type::float(),
                        'resolve' => fn($attr) => $attr['width'] ?? null
                        ],

                    'length' => [
                        'type' => Type::float(),
                        'resolve' => fn($attr) => $attr['length'] ?? null
                        ],
                    'weight' => [
                        'type' => Type::float(),
                        'resolve' => fn($attr) => $attr['weight'] ?? null
                        ],
                    'size' => [
                        'type' => Type::float(),
                        'resolve' => fn($attr) => $attr['size'] ?? null
                    ] 
                ]
            ]);

            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => [
                        'type' => Type::int(),
                        'resolve' => fn($product) => $product->getId()
                        ],
                    'name' => [
                        'type' => Type::string(),
                        'resolve' => fn($product) => $product->getName()
                        ],
                    'attributes' => [
                        'type' => $attributeType,
                        'resolve' => fn($product) => $product->getAttributes()
                    ] 
                ],
            ]);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::listOf($productType),

                        'resolve' => function () {
                            return [
                                new SimpleProduct(1, 'Product 1'),
                                new SimpleProduct(2, 'Product 2'),
                                new DVD(3, 'DVD 1', 5.5),
                                new Book(4, 'Book 1', 2),
                                new Furniture(5, 'Chair 1', 10, 20, 30)
                            ];
                        }
                    ],
                ],
            ]);
        
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
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
        
            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
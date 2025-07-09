<?php

namespace App\GraphQL;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use App\GraphQL\Types\QueryType;
use App\GraphQL\Types\MutationType;
use RuntimeException;
use Throwable;
use GraphQL\Error\DebugFlag;

class GraphQLServer
{
    public static function handle()
    {
        try {
            $queryType = new QueryType();
            $mutationType = new MutationType();

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
            $query = $input['query'] ?? null;
            $variableValues = $input['variables'] ?? null;
            $operationName = $input['operationName'] ?? null;

            if ($query === null) {
                throw new RuntimeException('GraphQL query is missing.');
            }

   
            $rootValue = []; 

            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues, $operationName);
            $output = $result->toArray();
            

        } catch (Throwable $e) {


            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'extensions' => [
                            'category' => 'internal',
                            'trace' => explode("\n", $e->getTraceAsString()),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ],
                    ],
                ],
            ];
            // Set appropriate HTTP status code for errors
            http_response_code(500);
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output); // Use echo instead of return for direct output
    }
}
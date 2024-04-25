<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Rest\SomethingProcessor;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Rest\SomethingProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    shortName: 'CabinetConnectorCampusonlineSomething',
    types: ['https://schema.org/Something'],
    operations: [
        new Get(
            uriTemplate: '/cabinet-connector-campusonline/somethings/{identifier}',
            openapiContext: [
                'tags' => ['Template'],
            ],
            provider: SomethingProvider::class
        ),
        new GetCollection(
            uriTemplate: '/cabinet-connector-campusonline/somethings',
            openapiContext: [
                'tags' => ['Template'],
            ],
            provider: SomethingProvider::class
        ),
        new Post(
            uriTemplate: '/cabinet-connector-campusonline/somethings',
            openapiContext: [
                'tags' => ['Template'],
                'requestBody' => [
                    'content' => [
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string'],
                                ],
                                'required' => ['name'],
                            ],
                            'example' => [
                                'name' => 'Example Name',
                            ],
                        ],
                    ],
                ],
            ],
            processor: SomethingProcessor::class
        ),
        new Delete(
            uriTemplate: '/cabinet-connector-campusonline/somethings/{identifier}',
            openapiContext: [
                'tags' => ['Template'],
            ],
            provider: SomethingProvider::class,
            processor: SomethingProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['CabinetConnectorCampusonlineSomething:output']],
    denormalizationContext: ['groups' => ['CabinetConnectorCampusonlineSomething:input']]
)]
class Something
{
    #[ApiProperty(identifier: true)]
    #[Groups(['CabinetConnectorCampusonlineSomething:output'])]
    private ?string $identifier = null;

    #[ApiProperty(iris: ['https://schema.org/name'])]
    #[Groups(['CabinetConnectorCampusonlineSomething:output', 'CabinetConnectorCampusonlineSomething:input'])]
    private ?string $name;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}

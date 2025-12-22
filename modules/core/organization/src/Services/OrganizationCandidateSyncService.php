<?php

namespace Modules\Organization\Services;

use Illuminate\Support\Arr;
use Jora\HrCandidateRpcClient\Clients\CandidateClient;
use Jora\HrCandidateRpcClient\DTO\Request\OrganizationCreateDto;
use Jora\HrCandidateRpcClient\DTO\Request\OrganizationInsertDto;
use Jora\HrCandidateRpcClient\DTO\Response\BaseResponse;
use Modules\Organization\Models\Organization;

class OrganizationCandidateSyncService
{
    public function __construct(
        protected CandidateClient $candidateClient
    ) {
    }

    public function sync(Organization $organization): BaseResponse
    {
        $payload = $this->makePayload($organization);

        return $this->candidateClient->createOrganization($payload);
    }

    /**
     * @param iterable<int, Organization> $organizations
     */
    public function syncMany(iterable $organizations): BaseResponse
    {
        $payloads = [];

        foreach ($organizations as $organization) {
            $payloads[] = $this->makePayload($organization);
        }

        return $this->candidateClient->insertOrganizations(new OrganizationInsertDto($payloads));
    }

    protected function makePayload(Organization $organization): OrganizationCreateDto
    {
        $organization->loadMissing('structure');

        return new OrganizationCreateDto(
            name: $this->resolveName($organization),
            foreign_id: $organization->getKey(),
            foreign_structure_id: (int) $organization->structure_id,
            slug: $organization->slug,
            hidden: $organization->hidden,
        );
    }

    protected function resolveName(Organization $organization): array
    {
        $name = $organization->name ?? [];

        if (is_array($name) && ! empty($name)) {
            return $name;
        }

        if (is_string($name) && filled($name)) {
            return [
                app()->getLocale() => $name,
            ];
        }

        $structureName = $organization->structure?->name ?? [];

        if (is_array($structureName)) {
            $filtered = Arr::where($structureName, static fn($value) => filled($value));

            if (! empty($filtered)) {
                return $filtered;
            }
        } elseif (filled($structureName)) {
            return [
                app()->getLocale() => $structureName,
            ];
        }

        return [
            app()->getLocale() => 'organization-' . $organization->getKey(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Modules\Organization\Services;

use Illuminate\Support\Collection;
use Modules\Organization\Models\Organization;
use Modules\Structure\Services\StructureHierarchyService;

class OrganizationHierarchyService
{
    public function __construct(
        private readonly Organization $organization,
        private readonly StructureHierarchyService $structureHierarchyService
    ) {
    }

    /**
     * Collect all descendant organization identifiers for both structure parent relations.
     *
     * @return array{organization_first_parent: int[], organization_second_parent: int[]}
     */
    public function collectDescendantOrganizationIds(int $organizationId, ?Collection $organizations = null): array
    {
        $organizations = $organizations ?? $this->loadOrganizations();
        $rootOrganization = $organizations->firstWhere('id', $organizationId);


        if (!$rootOrganization || is_null($rootOrganization->getAttribute('structure_id'))) {
            return [
                'organization_first_parent' => [],
                'organization_second_parent' => [],
            ];
        }

        $descendantStructureIds = $this->structureHierarchyService
            ->collectDescendantIds((int)$rootOrganization->getAttribute('structure_id'));

        $firstParentOrganizations = $this->collectOrganizationsForStructures(
            $organizations,
            $descendantStructureIds['child_first_parent']
        );

        $secondParentOrganizations = $this->collectOrganizationsForStructures(
            $organizations,
            $descendantStructureIds['child_second_parent']
        );

        return [
            'organization_first_parent' => $firstParentOrganizations,
            'organization_second_parent' => $secondParentOrganizations,
        ];
    }

    private function loadOrganizations(): Collection
    {
        return $this->organization
            ->newQuery()
            ->select(['id', 'structure_id'])
            ->get();
    }

    /**
     * @param int[] $structureIds
     *
     * @return int[]
     */
    private function collectOrganizationsForStructures(Collection $organizations, array $structureIds): array
    {
        if ($structureIds === []) {
            return [];
        }

        $structureLookup = array_fill_keys(array_map('intval', $structureIds), true);

        return $organizations
            ->filter(static function (Organization $organization) use ($structureLookup) {
                $structureId = $organization->getAttribute('structure_id');

                if (is_null($structureId)) {
                    return false;
                }

                return isset($structureLookup[(int)$structureId]);
            })
            ->pluck('id')
            ->map(static fn($id) => (int)$id)
            ->unique()
            ->values()
            ->all();
    }
}


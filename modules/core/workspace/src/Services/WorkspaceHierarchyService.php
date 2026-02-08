<?php

declare(strict_types=1);

namespace Modules\Workspace\Services;

use Illuminate\Support\Collection;
use Modules\Workspace\Models\Workspace;
use Modules\Structure\Services\StructureHierarchyService;

class WorkspaceHierarchyService
{
    public function __construct(
        private readonly Workspace $workspace,
        private readonly StructureHierarchyService $structureHierarchyService
    ) {
    }

    /**
     * Collect all descendant workspace identifiers for both structure parent relations.
     *
     * @return array{workspace_first_parent: int[], workspace_second_parent: int[]}
     */
    public function collectDescendantWorkspaceIds(int $workspaceId, ?Collection $workspaces = null): array
    {
        $workspaces = $workspaces ?? $this->loadWorkspaces();
        $rootWorkspace = $workspaces->firstWhere('id', $workspaceId);


        if (!$rootWorkspace || is_null($rootWorkspace->getAttribute('structure_id'))) {
            return [
                'workspace_first_parent' => [],
                'workspace_second_parent' => [],
            ];
        }

        $descendantStructureIds = $this->structureHierarchyService
            ->collectDescendantIds((int)$rootWorkspace->getAttribute('structure_id'));

        $firstParentWorkspaces = $this->collectWorkspacesForStructures(
            $workspaces,
            $descendantStructureIds['child_first_parent']
        );

        $secondParentWorkspaces = $this->collectWorkspacesForStructures(
            $workspaces,
            $descendantStructureIds['child_second_parent']
        );

        return [
            'workspace_first_parent' => $firstParentWorkspaces,
            'workspace_second_parent' => $secondParentWorkspaces,
        ];
    }

    private function loadWorkspaces(): Collection
    {
        return $this->workspace
            ->newQuery()
            ->select(['id', 'structure_id'])
            ->get();
    }

    /**
     * @param int[] $structureIds
     *
     * @return int[]
     */
    private function collectWorkspacesForStructures(Collection $workspaces, array $structureIds): array
    {
        if ($structureIds === []) {
            return [];
        }

        $structureLookup = array_fill_keys(array_map('intval', $structureIds), true);

        return $workspaces
            ->filter(static function (Workspace $workspace) use ($structureLookup) {
                $structureId = $workspace->getAttribute('structure_id');

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

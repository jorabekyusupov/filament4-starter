<?php

namespace Modules\Workspace\Services;

use Illuminate\Support\Arr;
use Jora\HrCandidateRpcClient\Clients\CandidateClient;
use Jora\HrCandidateRpcClient\DTO\Request\WorkspaceCreateDto;
use Jora\HrCandidateRpcClient\DTO\Request\WorkspaceInsertDto;
use Jora\HrCandidateRpcClient\DTO\Response\BaseResponse;
use Modules\Workspace\Models\Workspace;

class WorkspaceCandidateSyncService
{
    public function __construct(
        protected CandidateClient $candidateClient
    ) {
    }

    public function sync(Workspace $workspace): BaseResponse
    {
        $payload = $this->makePayload($workspace);

        return $this->candidateClient->createWorkspace($payload);
    }

    /**
     * @param iterable<int, Workspace> $workspaces
     */
    public function syncMany(iterable $workspaces): BaseResponse
    {
        $payloads = [];

        foreach ($workspaces as $workspace) {
            $payloads[] = $this->makePayload($workspace);
        }

        return $this->candidateClient->insertWorkspaces(new WorkspaceInsertDto($payloads));
    }

    protected function makePayload(Workspace $workspace): WorkspaceCreateDto
    {
        $workspace->loadMissing('structure');

        return new WorkspaceCreateDto(
            name: $this->resolveName($workspace),
            foreign_id: $workspace->getKey(),
            foreign_structure_id: (int) $workspace->structure_id,
            slug: $workspace->slug,
            hidden: $workspace->hidden,
        );
    }

    protected function resolveName(Workspace $workspace): array
    {
        $name = $workspace->name ?? [];

        if (is_array($name) && ! empty($name)) {
            return $name;
        }

        if (is_string($name) && filled($name)) {
            return [
                app()->getLocale() => $name,
            ];
        }

        $structureName = $workspace->structure?->name ?? [];

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
            app()->getLocale() => 'workspace-' . $workspace->getKey(),
        ];
    }
}

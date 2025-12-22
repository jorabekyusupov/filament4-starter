<?php

namespace Modules\User\Services;

use Jora\HrCandidateRpcClient\Clients\CandidateClient;
use Jora\HrCandidateRpcClient\DTO\Request\UserCreateDto;
use Jora\HrCandidateRpcClient\DTO\Request\UserInsertDto;
use Jora\HrCandidateRpcClient\DTO\Response\BaseResponse;
use Modules\User\Models\User;

class UserCandidateSyncService
{
    public function __construct(
        protected CandidateClient $candidateClient
    ) {
    }

    public function sync(User $user): BaseResponse
    {
        $payload = $this->makeUserPayload($user);

        return $this->candidateClient->createUser($payload);
    }

    /**
     * @param iterable<int, User> $users
     */
    public function syncMany(iterable $users): BaseResponse
    {
        $payload = [];

        foreach ($users as $user) {
            $payload[] = $this->makeUserPayload($user);
        }

        return $this->candidateClient->insertUsers(new UserInsertDto($payload));
    }

    protected function makeUserPayload(User $user): UserCreateDto
    {
        return new UserCreateDto(
            pin: (string) $user->pin,
            username: (string) $user->username,
            password: (string) $user->password,
            first_name: $user->first_name,
            last_name: $user->last_name,
            middle_name: $user->middle_name,
            foreign_id: $user->getKey(),
            foreign_organization_id: $user->organization_id,
        );
    }
}

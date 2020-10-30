<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

use \Psr\Http\Message\ServerRequestInterface;

class UserController
{
    public function createUser(ServerRequestInterface $request, UserRepo $userRepo)
    {
        $userCreateParams = UserCreateParams::createFromRequest($request);

        $user = $userRepo->createUser($userCreateParams);
        // The user entity only exists after it has been persisted in the DB.
        //
        // There is never a 'virtual' user entity that can be referenced before
        // it actually exists for real.

        return SuccessResponse(/* Whatever*/);
    }
}

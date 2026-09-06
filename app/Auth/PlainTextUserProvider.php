<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Custom user provider that compares passwords as plain text.
 * Extends EloquentUserProvider so all other auth behaviour (retrieving
 * users, remember tokens, etc.) stays exactly the same.
 */
class PlainTextUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     * Instead of bcrypt comparison, we do a direct string match.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = $credentials['password'];

        // Direct plain-text comparison
        return $plain === $user->getAuthPassword();
    }

    /**
     * Rehashing is not needed for plain-text passwords.
     */
    public function rehashPasswordIfRequired(
        Authenticatable $user,
        array $credentials,
        bool $force = false
    ): void {
        // No-op — plain text never needs rehashing
    }
}

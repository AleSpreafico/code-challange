<?php

namespace App\Policies;

use App\Models\Events;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Events  $events
     * @return bool
     */
    public function update(User $user, Events $events): bool
    {
        return $user->id === $events->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Events  $events
     * @return bool
     */
    public function delete(User $user, Events $events): bool
    {
        if ($events->hasComments()) {
            return false;
        }

        return $this->update($user, $events);
    }
}

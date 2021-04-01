<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\News  $news
     * @return bool
     */
    public function update(User $user, News $news): bool
    {
        return $user->id === $news->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\News  $news
     * @return bool
     */
    public function delete(User $user, News $news): bool
    {
        if ($user->id !== $news->user_id) {
            return false;
        }

        if ($news->hasComments()) {
            return false;
        }

        return true;
    }
}

<?php

namespace Pickems\Transformers;

use Pickems\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => (int) $user->id,
            'name' => $user->first_name,
            'email' => $user->email,
        ];
    }
}

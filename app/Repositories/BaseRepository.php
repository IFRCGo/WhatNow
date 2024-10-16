<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;


abstract class BaseRepository
{

    
    public function save(Model $model)
    {
        $saved = $model->save();

        if ($saved) {
            app('cache')->flush();
        }

        return $saved;
    }

    
    public function update(Model $model, array $input)
    {
        $updated = $model->update($input);

        if ($updated) {
            app('cache')->flush();
        }

        return $updated;
    }

    
    public function delete(Model $model)
    {
        $deleted = $model->delete();

        if ($deleted) {
            app('cache')->flush();
        }

        return $deleted;
    }

    
    public function forceDelete(Model $model)
    {
        $deleted = $model->forceDelete();

        if ($deleted) {
            app('cache')->flush();
        }

        return $deleted;
    }

    
    public function restore(Model $model)
    {
        $deleted = $model->restore();

        if ($deleted) {
            app('cache')->flush();
        }

        return $deleted;
    }

    
    protected function query()
    {
        return call_user_func(static::MODEL . '::query');
    }
}

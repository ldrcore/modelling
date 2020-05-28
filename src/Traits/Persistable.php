<?php

namespace LDRCore\Modelling\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait Persistable
 *
 * @package LDRCore\Modelling\Traits
 * @property Model $model
 */
trait Persistable
{
    /**
     * @param array $data
     * @return Model
     */
    public function insert($data = []) : Model
    {
        return $this->model::create($data);
    }
    /**
     * @param array $data
     * @return Model
     */
    public function updateOrInsert($data = []) : Model
    {
        return $this->model::updateOrCreate($data);
    }
    /**
     * @param int|array $ids
     * @param array $data
     * @return int
     */
    public function update($ids, $data = []) : int
    {
        return $this->model::whereIn('id', collect($ids))->update($data);
    }
    /**
     * @param int|array $ids
     * @return int
     */
    public function delete($ids) : int
    {
        return $this->model::whereIn('id', collect($ids))->delete();
    }
}

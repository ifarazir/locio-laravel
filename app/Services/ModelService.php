<?php
namespace App\Services;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Imanghafoori\Helpers\Nullable;
use InvalidArgumentException;
abstract class ModelService
{
    /**
     * @var Model|mixed
     */
    protected Model $model;
    /**
     * @var array|[]callable
     */
    protected array $beforeCallbacks = [];
    /**
     * @var array|[]callable
     */
    protected array $afterCallbacks = [];
    /**
     * @return bool
     * @throws InvalidArgumentException
     */
    public function checkModelExists(): bool
    {
        if (!$this->model) {
            throw new InvalidArgumentException('Set argument $this->model this service');
        }
        return true;
    }
    /**
     * @return $this
     */
    public function lockForUpdate()
    {
        $this->model->lockForUpdate();
        return $this;
    }
    /**
     * @param  array  $data
     * @return Nullable
     */
    public function updateOrCreate($data = []): ?Nullable
    {
        if (!$this->model->exists) {
            return $this->create($data);
        } else {
            return $this->update($data);
        }
    }
    /**
     * @return Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }
    /**
     * @param  Model  $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }
    public function findByIdWithRelation(int $id, array $relation = [])
    {
        return $this->model->query()->with($relation)->find($id);
    }
    public function allWithRelation(array $relation = [])
    {
        if (request()->pagination) {
            return $this->model->query()->with($relation)->orderByDesc('created_at')->paginate(request()->pagination);
        } else {
            return $this->model->query()->with($relation)->orderByDesc('created_at')->get();
        }
    }
    /**
     * @param  array  $data
     * @return Nullable
     */
    public function create(array $data = []): ?Nullable
    {
        $beforeCallback = function ($model, $service) {
            if ($model->exists) {
                return false;
            }
            return true;
        };
        $this->beforeCallback($beforeCallback);
        try {
            $result = $this->save($data);
        } catch (\Throwable $th) {
            logger($th);
            return nullable(null);
        }
        return ($result) ? nullable($this->getModel()) : nullable(null);
    }
    /**
     * @param  array  $data
     * @return Nullable
     */
    public function update($data = []): Nullable
    {
        $result = $this->save($data);
        return ($result) ? nullable($this->getModel()) : nullable(null);
    }
    /**
     * @return Nullable
     */
    public function delete(): ?Nullable
    {
        try {
            $result = $this->destroy();
        } catch (\Throwable $th) {
            logger($th);
            return nullable(null);
        }
        return ($result) ? nullable($this->getModel()) : nullable(null);
    }
    /**
     * @param  callable  $callback
     * @return $this
     */
    public function beforeCallback(callable $callback): self
    {
        $this->beforeCallbacks[] = $callback;
        return $this;
    }
    /**
     * @param  callable  $callback
     * @return $this
     */
    public function afterCallback(callable $callback): self
    {
        $this->afterCallbacks[] = $callback;
        return $this;
    }
    /**
     * @param  array  $data
     * @return bool
     */
    protected function save(array $data = []): bool
    {
        return DB::transaction(function () use ($data) {
            $this->checkModelExists();
            $this->model->fill($data);
            // Before callback
            $beforeCallbacks = $this->beforeCallbacks;
            if (is_array($beforeCallbacks)) {
                foreach ($beforeCallbacks as $beforeCallback) {
                    if ($beforeCallback instanceof Closure) {
                        if ($beforeCallback($this->model, $this) === false) {
                            // stop continue and save model
                            return false;
                        }
                    }
                }
            }
            $save = $this->model->save();
            // After callback
            $afterCallbacks = $this->afterCallbacks;
            if (is_array($afterCallbacks)) {
                foreach ($afterCallbacks as $afterCallback) {
                    if ($afterCallback instanceof Closure) {
                        $afterCallback($this->model, $this);
                        $save = $this->model->save();
                    }
                }
            }
            return $save;
        });
    }
    /**
     * @return bool|null
     */
    protected function destroy(): ?bool
    {
        return DB::transaction(function () {
            // Before callback
            $beforeCallbacks = $this->beforeCallbacks;
            if (is_array($beforeCallbacks)) {
                foreach ($beforeCallbacks as $beforeCallback) {
                    if ($beforeCallback instanceof Closure) {
                        if ($beforeCallback($this->model, $this) === false) {
                            // stop continue and save model
                            return false;
                        }
                    }
                }
            }
            $delete = $this->model->delete();
            // After callback
            $afterCallbacks = $this->afterCallbacks;
            if (is_array($afterCallbacks)) {
                foreach ($afterCallbacks as $afterCallback) {
                    if ($afterCallback instanceof Closure) {
                        $afterCallback($this->model, $this);
                    }
                }
            }
            return $delete;
        });
    }
}

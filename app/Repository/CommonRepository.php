<?php

namespace App\Repository;

use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use DB;

class CommonRepository implements RepositoryInterface
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model
    public function all()
    {
        return $this->model->all();
    }

    // create a new record in the database
    public function create(array $data)
    { 
        return $this->model->create($data);
    }

    // update record in the database
    public function update(array $data, $id)
    {   
        $record = $this->model->find($id);
        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    // show the record with the given id
    public function findAttrByPk($tableAndField, $pk)
    {
        $tableAndField = explode(':', $tableAndField);
        $table = $tableAndField[0];
        $field = $tableAndField[1];
        $data = DB::table($table)->select("$field as returnField")->where('id', $pk)->first();
        if($data)
        {
            return $data->returnField;
        }else{
            return 'N/A';
        }
    }
}

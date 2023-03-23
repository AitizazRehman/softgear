<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\UploadRepository;
use App\Models\Upload;
use App\Validators\UploadValidator;

/**
 * Class UploadRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UploadRepositoryEloquent extends BaseRepository implements UploadRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Upload::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    public function upload($data,$obj)
    {
      if($data)
      {
          $file = $data;
          $file_name = preg_replace("/[^a-z0-9\_\-\.]/i", "_", $file->getClientOriginalName());
          $name = date("hismdy") . '_' . $obj->id . '_'. str_replace(' ', '_', $file_name );
          $destinationPath =  public_path('\uploads\ProfilePicture');
          $file->move($destinationPath, $name);
          $ext = $file->getClientOriginalExtension();
          $image = 0;
          if(in_array(strtolower($ext) , ['jpeg','jpe', 'jpg', 'gif', 'png', 'webp'] )){
            $image = 1;
          }
           $obj->upload()->updateOrCreate([
                            'uploadable_id' => $obj->id,
                          ],[
                            'name' => $name, 'is_image'=> $image,
                          ]);
        }
        return ;
    }
}

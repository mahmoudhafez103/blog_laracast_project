<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    //protected $fillable=['category_id','title','user_id','body','slug','excerpt'];
        // this is the same as eager loading with load() function
    protected $with =['category','author','comments'];

    public function scopeFilter($query,array $filters)
    {
        //dd($filters);
        $query->when($filters['search']??false,function($query,$search){
            $query->where(fn($query)=>
                $query->where('title', 'like', '%' . $search . '%')
                ->orwhere('body', 'like', '%' . $search . '%'));
        });
        $query->when($filters['category']??false, fn($query,$category)=>
            $query->whereHas('category',fn($query)=>
            $query->where('slug',$category))

        //this is with simple sql queries
//            $query->whereExists(fn ($query)=>
//                    $query->from('categories')
//                        ->whereColumn('categories.id','posts.category_id')
//                        ->where('categories.slug',$category)
//                )
        );
        $query->when($filters['author']??false, fn($query,$author)=>
        $query->whereHas('author',fn($query)=>
        $query->where('username',$author))


        );
    }
    function getRouteKeyName()
    {
        return 'slug'; // TODO: Change the autogenerated stub
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function category(){
       return  $this->belongsTo(Category::class);
    }
    public function author(){
        return $this->belongsTo(User::class,'user_id');
    }
}

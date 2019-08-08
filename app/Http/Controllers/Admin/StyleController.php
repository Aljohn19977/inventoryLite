<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Brand;
use App\Category;
use App\Style;
use Carbon\Carbon;
use Milon\Barcode\DNS1D;


class StyleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.style.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();

        $sku_id_not_clean = preg_replace("/[:-]/","", Carbon::now());
        $sku_style_id = preg_replace('/\s+/', '', 'STY-'.$sku_id_not_clean);

        return view('admin.style.add',compact('categories','brands','sku_style_id'));
    }

    public function getSkuId()
    {
        $sku_id_not_clean = preg_replace("/[:-]/","", Carbon::now());
        $sku_style_id = preg_replace('/\s+/', '', 'STY-'.$sku_id_not_clean);
        
        return response()->json(['sku_style_id'=>$sku_style_id]);
    }

    public function getStyleInfo($id){

        $style = Style::findOrFail($id);

        return response()->json([
                'sku_style_id'=>$style->sku_style_id,
                'name'=>$style->name,
                'category_id'=>$style->category->name,
                'brand_id'=>$style->brand->name,
                'date_added'=>$style->created_at->format('M - d - Y'),
                'status'=>$style->status,
                'description'=>$style->description
                ]);

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'sku_style_id' => 'required',
            'name' => 'required',
            'brand_id' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);
     
        $style = new Style;
        $style->sku_style_id = $request->sku_style_id;
        $style->name = $request->name;
        $style->brand_id = $request->brand_id;
        $style->category_id = $request->category_id;
        $style->description = $request->description;
        $style->item_qty = 0;
        $style->display_qty = 0;
        $style->item_qty_status = 'Out of Stock';
        $style->display_qty_status = 'No Display';
        $style->status = $request->status;
        $style->save();

        Storage::disk('public')->put($request->sku_style_id.'.png',base64_decode(DNS1D::getBarcodePNG($request->sku_style_id, "C93",1,40)));

        return response()->json(['success'=>'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $styles = Style::findOrFail($id);
        $brands = Brand::all();
        $categories = Category::all();
 
        return view('admin.style.view',compact('styles','brands','categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $styles = Style::findOrFail($id);
       $brands = Brand::all();
       $categories = Category::all();

       return view('admin.style.update',compact('styles','brands','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'sku_style_id' => 'required',
            'name' => 'required',
            'brand_id' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);

        $style = Style::findOrfail($id);
        $style->sku_style_id = $request->sku_style_id;
        $style->name = $request->name;
        $style->brand_id = $request->brand_id;
        $style->category_id = $request->category_id;
        $style->description = $request->description;
        $style->status = $request->status;
        $style->update();

        return response()->json(['success'=>'Success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $style=Style::findOrfail($id);
       $style->delete();
    }

    public function apiGetDeactiveStyle(Request $request){

        $columns = array(
          0 => 'sku_style_id',
          1 => 'name',
          2 => 'brand_id',
          3 => 'category_id',
          4 => 'status',  
          5 => 'created_at'
        );
  
  
   
   
        // this will return the # of rows
   
        $totalData = Style::where('status','=', 'Deactive')->count();
       
        //static requests
   
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
   
        //end of static requests
       
   
        //this enables the search function on the datatables blade view
        if (empty($request->input('search.value')))
        {
   
          //query if no values on search text
   
            $styles = Style::where('status', '=', 'Deactive')
                  ->join('brands', 'styles.brand_id', '=', 'brands.id')
                  ->join('categories', 'styles.category_id', '=', 'categories.id')
                  ->offset($start)
                  ->limit($limit)
                  ->orderBy($order,$dir)
                  ->select('styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
                  ->get();
    
                  //return # of rows filtered (just copy the query of the post above and remove the get() and change to count() to return the # of rows)
   
            
                  $totalFiltered = Style::where('status', '=', 'Active')->count();

               
                 
        }
        else
        {
           $search = $request->input('search.value');
   
           // if search has a value (you can use inner join)
   
           $styles = Style::join('brands', 'styles.brand_id', '=', 'brands.id')
                  ->join('categories', 'styles.category_id', '=', 'categories.id')
                  ->WhereRaw('(styles.status = "Deactive" AND styles.id LIKE ?)', "%{$search}%")
                  ->orWhereRaw('(styles.status = "Deactive" AND styles.id and styles.name LIKE ?)', "%{$search}%")
                  ->orWhereRaw('(styles.status = "Deactive" AND styles.id and categories.name LIKE ?)', "%{$search}%")
                  ->orWhereRaw('(styles.status = "Deactive" AND styles.id and brands.name LIKE ?)', "%{$search}%")
                  ->offset($start)
                  ->limit($limit)
                  ->orderBy($order,$dir)
                  ->select('styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
                  ->get();

                 
   
            //copy
   
           $totalFiltered = Style::join('brands', 'styles.brand_id', '=', 'brands.id')
           ->join('categories', 'styles.category_id', '=', 'categories.id')
           ->WhereRaw('(styles.status = "Deactive" AND styles.id LIKE ?)', "%{$search}%")
           ->orWhereRaw('(styles.status = "Deactive" AND styles.id and styles.name LIKE ?)', "%{$search}%")
           ->orWhereRaw('(styles.status = "Deactive" AND styles.id and categories.name LIKE ?)', "%{$search}%")
           ->orWhereRaw('(styles.status = "Deactive" AND styles.id and brands.name LIKE ?)', "%{$search}%")
           ->offset($start)
           ->limit($limit)
           ->orderBy($order,$dir)
           ->select('styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
           ->get()
           ->count();
                  //return # of rows filtered (just copy the query of the post above and remove the get() and change to count() to return the # of rows)
          
        }
   
       
        //data to store the data's of the results
        $data = array();
        $data_status = $request->status;
  
        if ($styles)
        {
          foreach ($styles as $value) {
  
                    $nestedData['sku_style_id'] = $value->sku_style_id;
                    $nestedData['name'] = $value->name;
                    $nestedData['brand_id'] = $value->brand_id;
                    $nestedData['category_id'] = $value->category_id;
                    $nestedData['status']  = '<div class="btn btn-block btn-danger btn-sm">'.$value->status.'</div>';
                    $nestedData['action'] = '<div class="btn-group">
                                                              <button type="button" class="btn btn-primary">Action</button>
                                                                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                  <span class="caret"></span>
                                                                  <span class="sr-only">Toggle Dropdown</span>
                                                              </button>
                                                              <ul class="dropdown-menu" role="menu">
                                                                <li><a class="arrow" href="/style/'.$value->id.'">View</a></li>
                                                                <li><a class="arrow" href="/style/edit/'.$value->id.'">Edit</a></li>
                                                                <li><a class="arrow" onclick="delete_style_info('.$value->id.')">Delete</a></li>
                                                              </ul>
                                                          </div>';  
                    $nestedData['created_at'] = $value->created_at->format('M - d - Y'); 
                                  //pass to data
                   $data[] = $nestedData;
  
          }
        }
   
   
        //return this json encoded!
        $json_data = array(
          "draw" => ($request->draw ? intval($request->draw):0), //draw for pagination
          "recordsTotal" => intval($totalData), //total records
          "recordsFiltered" => intval($totalFiltered), //results of filter
          "data" => $data, //data
        );
   
        //like this
        return json_encode($json_data);
  
  
    }

    public function apiGetActiveStyle(Request $request){

        $columns = array(
          0 => 'sku_style_id',
          1 => 'name',
          2 => 'brand_id',
          3 => 'category_id',
          4 => 'status',  
          5 => 'created_at'
        );
  
  
   
   
        // this will return the # of rows
   
        $totalData = Style::where('status','=', 'Active')->count();
       
        //static requests
   
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
   
        //end of static requests
       
   
        //this enables the search function on the datatables blade view
        if (empty($request->input('search.value')))
        {
   
          //query if no values on search text
   
            $styles = Style::where('status', '=', 'Active')
                  ->join('brands', 'styles.brand_id', '=', 'brands.id')
                  ->join('categories', 'styles.category_id', '=', 'categories.id')
                  ->offset($start)
                  ->limit($limit)
                  ->orderBy($order,$dir)
                  ->select('styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
                  ->get();
    
                  //return # of rows filtered (just copy the query of the post above and remove the get() and change to count() to return the # of rows)
   
            
                  $totalFiltered = Style::where('status', '=', 'Active')->count();

               
                 
        }
        else
        {
           $search = $request->input('search.value');
   
           // if search has a value (you can use inner join)
   
           $styles = Style::join('brands', 'styles.brand_id', '=', 'brands.id')
                  ->join('categories', 'styles.category_id', '=', 'categories.id')
                  ->WhereRaw('(styles.status = "Active" AND styles.id LIKE ?)', "%{$search}%")
                  ->orWhereRaw('(styles.status = "Active" AND styles.id and styles.name LIKE ?)', "%{$search}%")
                  ->orWhereRaw('(styles.status = "Active" AND styles.id and categories.name LIKE ?)', "%{$search}%")
                  ->orWhereRaw('(styles.status = "Active" AND styles.id and brands.name LIKE ?)', "%{$search}%")
                  ->offset($start)
                  ->limit($limit)
                  ->orderBy($order,$dir)
                  ->select('styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
                  ->get();

                 
   
            //copy
   
           $totalFiltered = Style::join('brands', 'styles.brand_id', '=', 'brands.id')
           ->join('categories', 'styles.category_id', '=', 'categories.id')
           ->WhereRaw('(styles.status = "Active" AND styles.id LIKE ?)', "%{$search}%")
           ->orWhereRaw('(styles.status = "Active" AND styles.id and styles.name LIKE ?)', "%{$search}%")
           ->orWhereRaw('(styles.status = "Active" AND styles.id and categories.name LIKE ?)', "%{$search}%")
           ->orWhereRaw('(styles.status = "Active" AND styles.id and brands.name LIKE ?)', "%{$search}%")
           ->offset($start)
           ->limit($limit)
           ->orderBy($order,$dir)
           ->select('styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
           ->get()
           ->count();
                  //return # of rows filtered (just copy the query of the post above and remove the get() and change to count() to return the # of rows)
          
        }
   
       
        //data to store the data's of the results
        $data = array();
        $data_status = $request->status;
  
        if ($styles)
        {
          foreach ($styles as $value) {
  
                    $nestedData['sku_style_id'] = $value->sku_style_id;
                    $nestedData['name'] = $value->name;
                    $nestedData['brand_id'] = $value->brand_id;
                    $nestedData['category_id'] = $value->category_id;
                    $nestedData['status']  = '<div class="btn btn-block btn-success btn-sm">'.$value->status.'</div>';
                    $nestedData['action'] = '<div class="btn-group">
                                                              <button type="button" class="btn btn-primary">Action</button>
                                                                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                  <span class="caret"></span>
                                                                  <span class="sr-only">Toggle Dropdown</span>
                                                              </button>
                                                              <ul class="dropdown-menu" role="menu">
                                                                  <li><a class="arrow" href="/style/'.$value->id.'">View</a></li>
                                                                  <li><a class="arrow" href="/style/edit/'.$value->id.'">Edit</a></li>
                                                                  <li><a class="arrow" onclick="delete_style_info('.$value->id.')">Delete</a></li>
                                                              </ul>
                                                          </div>';  
                    $nestedData['created_at'] = $value->created_at->format('M - d - Y'); 
                                  //pass to data
                   $data[] = $nestedData;
  
          }
        }
   
   
        //return this json encoded!
        $json_data = array(
          "draw" => ($request->draw ? intval($request->draw):0), //draw for pagination
          "recordsTotal" => intval($totalData), //total records
          "recordsFiltered" => intval($totalFiltered), //results of filter
          "data" => $data, //data
        );
   
        //like this
        return json_encode($json_data);
  
  
    }

    public function apiGetAllStyle(Request $request){

        $columns = array(
          0 => 'sku_style_id',
          1 => 'name',
          2 => 'brand_id',
          3 => 'category_id',
          4 => 'status',  
          5 => 'created_at'
        );
  
  
   
   
        // this will return the # of rows
   
        $totalData = Style::all()->count();
       
        //static requests
   
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
   
        //end of static requests
       
   
        //this enables the search function on the datatables blade view
        if (empty($request->input('search.value')))
        {
   
          //query if no values on search text
   
            $styles = Style::offset($start)
                  ->limit($limit)
                  ->orderBy($order,$dir)
                  ->get(['id','sku_style_id','name','brand_id','category_id','status','created_at']);
    
                  //return # of rows filtered (just copy the query of the post above and remove the get() and change to count() to return the # of rows)
   
                  $totalFiltered = Style::all()->count();
        }
        else
        {
           $search = $request->input('search.value');
   
           // if search has a value (you can use inner join)
   
           $styles = Style::WhereRaw('(sku_style_id LIKE ?)', "%{$search}%")
                  ->offset($start)
                  ->limit($limit)
                  ->orderBy($order,$dir)
                  ->get(['id','sku_style_id','name','brand_id','category_id','status','created_at']);
   
   
            //copy
   
           $totalFiltered = Style::WhereRaw('(sku_style_id LIKE ?)', "%{$search}%")
                  ->count();
                  //return # of rows filtered (just copy the query of the post above and remove the get() and change to count() to return the # of rows)
          
        }
   
       
        //data to store the data's of the results
        $data = array();
        $data_status = $request->status;
  
        if ($styles)
        {
          foreach ($styles as $value) {
  
                

                    if ($value->status=='Active'){
                        $status = '<div class="btn btn-block btn-success btn-sm">'.$value->status.'</div>';
                    }else if($value->status=='Deactive'){
                        $status = '<div class="btn btn-block btn-danger btn-sm">'.$value->status.'</div>';
                    }else{
                        $status = '<div class="btn btn-block btn-danger btn-sm">'.$value->status.'</div>';
                    }
  
                    $nestedData['sku_style_id'] = $value->sku_style_id;
                    $nestedData['name'] = $value->name;
                    $nestedData['brand_id'] = $value->brand->name;
                    $nestedData['category_id'] = $value->category->name;
                    $nestedData['status']  = $status;
                    $nestedData['action'] = '<div class="btn-group">
                                                              <button type="button" class="btn btn-primary">Action</button>
                                                                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                  <span class="caret"></span>
                                                                  <span class="sr-only">Toggle Dropdown</span>
                                                              </button>
                                                              <ul class="dropdown-menu" role="menu">
                                                                <li><a class="arrow" href="/style/'.$value->id.'">View</a></li>
                                                                <li><a class="arrow" href="/style/edit/'.$value->id.'">Edit</a></li>
                                                                <li><a class="arrow" onclick="delete_style_info('.$value->id.')">Delete</a></li>
                                                              </ul>
                                                          </div>';  
                    $nestedData['created_at'] = $value->created_at->format('M - d - Y'); 
                                  //pass to data
                   $data[] = $nestedData;
  
          }
        }
   
   
        //return this json encoded!
        $json_data = array(
          "draw" => ($request->draw ? intval($request->draw):0), //draw for pagination
          "recordsTotal" => intval($totalData), //total records
          "recordsFiltered" => intval($totalFiltered), //results of filter
          "data" => $data, //data
        );
   
        //like this
        return json_encode($json_data);
  
  
      }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Style;
use App\Brand;
use App\Category;
use App\Item;
 
class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.item.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'style_size.*'=> 'required|string',
            'style_color.*'=> 'required|string',
            'quantity.*'=> 'required|integer',
            'style_id' => 'required',
        ]);

        
        
        $item_qty = 0;

        for($count = 0; $count < count($request->style_size); $count++)
         {  
            $quantity = $request->quantity[$count];
                
            for ($i=0; $i < $quantity; $i++) { 

                
                $sku_id_not_clean = preg_replace("/[:-]/","", Carbon::now()->addSeconds(420));
                $sku_item_id = preg_replace('/\s+/', '', 'ITM-'.$sku_id_not_clean);

                $item = new Item;
                $item->style_id = $request->style_id;
                $item->size = $request->style_size[$count];
                $item->color = $request->style_color[$count];
                $item->status = 'Stored';
                $item->save();


                $item_qty++;
            }
         }
                    
        if(Style::where('id', '=', $request->style_id)->exists()) {
            $style = Style::where('id', '=', $request->style_id)->increment('item_qty',$item_qty);
            $style = Style::where('id', '=', $request->style_id)->update(['item_qty_status'=>'In Stock']);
        }else{
            $style = new Style;
            $style->style_id = $request->style_id;
            $style->brand_name = $request->brand_name;
            $style->category_name = $request->category_name;
            $style->style_name = $request->style_name;
            $style->style_description = $request->style_description;
            $style->stock_qty = $stock_qty;
            $style->display_qty = 0;
            $style->status = 'Undisplayed';
            $style->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function addItem($id){

        $styles = Style::findOrFail($id);
        $brands = Brand::all();
        $categories = Category::all();
 
        return view('admin.item.addItem',compact('styles','brands','categories'));
    }

    public function apiGetItemActiveStyle(Request $request){

        $columns = array(
            0 => 'sku_style_id',
            1 => 'name',
            2 => 'brand_id',
            3 => 'category_id',
            4 => 'item_qty',
            5 => 'created_at',  
            6 => 'status',
            7 => 'action'
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
                    ->select('styles.item_qty_status','styles.item_qty','styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
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
                    ->WhereRaw('(styles.status = "Active" AND styles.sku_style_id LIKE ?)', "%{$search}%")
                    ->orWhereRaw('(styles.status = "Active" AND styles.sku_style_id and styles.name LIKE ?)', "%{$search}%")
                    ->orWhereRaw('(styles.status = "Active" AND styles.sku_style_id and categories.name LIKE ?)', "%{$search}%")
                    ->orWhereRaw('(styles.status = "Active" AND styles.sku_style_id and brands.name LIKE ?)', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->select('styles.item_qty_status','styles.item_qty','styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
                    ->get();
  
                   
     
              //copy
     
             $totalFiltered = Style::join('brands', 'styles.brand_id', '=', 'brands.id')
             ->join('categories', 'styles.category_id', '=', 'categories.id')
             ->WhereRaw('(styles.status = "Active" AND styles.sku_style_id LIKE ?)', "%{$search}%")
             ->orWhereRaw('(styles.status = "Active" AND styles.sku_style_id and styles.name LIKE ?)', "%{$search}%")
             ->orWhereRaw('(styles.status = "Active" AND styles.sku_style_id and categories.name LIKE ?)', "%{$search}%")
             ->orWhereRaw('(styles.status = "Active" AND styles.sku_style_id and brands.name LIKE ?)', "%{$search}%")
             ->offset($start)
             ->limit($limit)
             ->orderBy($order,$dir)
             ->select('styles.item_qty_status','styles.item_qty','styles.id','styles.sku_style_id','styles.name','brands.name AS brand_id','categories.name AS category_id','styles.status','styles.created_at')
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

                      if ($value->item_qty_status == 'In Stock'){
                        $nestedData['status']  = '<div class="btn btn-block btn-success btn-sm">'.$value->item_qty_status.'</div>';
                      }else{
                        $nestedData['status']  = '<div class="btn btn-block btn-danger btn-sm">'.$value->item_qty_status.'</div>';
                      }
                      $nestedData['sku_style_id'] = $value->sku_style_id;
                      $nestedData['name'] = $value->name;
                      $nestedData['brand_id'] = $value->brand_id;
                      $nestedData['category_id'] = $value->category_id;
                      $nestedData['quantity'] =  $value->item_qty;
                      $nestedData['created_at'] = $value->created_at->format('M - d - Y');
                      $nestedData['action'] = '<div class="btn-group">
                                                                <button type="button" class="btn btn-primary">Action</button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                    <span class="caret"></span>
                                                                    <span class="sr-only">Toggle Dropdown</span>
                                                                </button>
                                                                <ul class="dropdown-menu" role="menu">
                                                                    <li><a class="arrow" href="/item/add/'.$value->id.'">Add Items</a></li>
                                                                    <li><a class="arrow" href="/style/edit/'.$value->id.'">View Style Info</a></li>
                                                                    <li><a class="arrow" onclick="delete_style_info('.$value->id.')">View Items</a></li>
                                                                </ul>
                                                            </div>';  
                     
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
